<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AgeGroup;
use App\Models\City;
use App\Models\Club;
use App\Models\Country;
use App\Models\GameMatch;
use App\Models\Gender;
use App\Models\State;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\Venue;
use App\Models\SportClassificationGroup;
use App\Models\SportClassificationOption;
use App\Services\Tournament\TournamentCreationService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class TournamentController extends Controller
{
    public function __construct(private TournamentCreationService $tournamentCreator)
    {
    }

    public function index()
    {
        $tournaments = Tournament::with(['hostClub', 'venue', 'city', 'state', 'country'])
            ->latest()
            ->paginate(20);
        return view('admin.tournaments.index', compact('tournaments'));
    }

    public function show(Tournament $tournament)
    {
        $tournament->load(['hostClub', 'teams', 'scheduledGames.referee', 'scheduledGames.homeClub', 'scheduledGames.awayClub']);

        // Get all matches for this tournament with their applications
        $matches = GameMatch::where('tournament_id', $tournament->id)
            ->with(['homeClub', 'awayClub', 'referee', 'applications.referee.user'])
            ->orderBy('match_date')
            ->orderBy('match_time')
            ->get();

        return view('admin.tournaments.show', compact('tournament', 'matches'));
    }

    public function create()
    {
        $clubs = Club::pluck('name', 'id');
        // Teams will be loaded dynamically based on host club sport
        $teams = collect();
        $countries = Country::orderBy('name')->get(['id', 'name']);
        $selectedCountryId = old('country_id');
        $states = $selectedCountryId
            ? State::where('country_id', $selectedCountryId)->orderBy('name')->get(['id', 'name'])
            : collect();

        $selectedStateId = old('state_id');
        $cities = $selectedStateId
            ? City::where('state_id', $selectedStateId)->orderBy('name')->get(['id', 'name'])
            : collect();

        $selectedCityId = old('city_id');
        $venues = $selectedCityId
            ? Venue::where('city_id', $selectedCityId)->orderBy('name')->get(['id', 'name', 'location'])
            : collect();

        $selectedClubId = old('host_club_id');
        $sportId = $selectedClubId ? Club::whereKey($selectedClubId)->value('sport_id') : null;
        $eligibilityData = $this->getEligibilityData($sportId);

        $formats = ['1' => 'RoundRobin', '2' => 'Knockout', '3' => 'group'];

        return view('admin.tournaments.create', compact('clubs', 'formats', 'teams', 'countries', 'states', 'cities', 'venues', 'eligibilityData'));
    }

    /**
     * Return teams that match the host club's sport (JSON)
     */
    public function teamsForHostClub(Request $request, Club $club)
    {
        $sportId = $club->sport_id;
        $divisionId = $request->query('division_id');
        $genderId = $request->query('gender_id');
        $ageGroupId = $request->query('age_group_id');
        $classificationOptionIds = collect($request->query('classification_option_ids', []))
            ->flatten()
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $teamsQuery = Team::where('sport_id', $sportId)
            ->with(['club', 'division'])
            ->orderBy('name')
            ->when($divisionId, function ($query) use ($divisionId) {
                $query->where('division_id', $divisionId);
            })
            ->when($genderId, function ($query, $genderId) {
                $query->where('gender_id', $genderId);
            })
            ->when($ageGroupId, function ($query, $ageGroupId) {
                $query->where('age_group_id', $ageGroupId);
            });

        if ($classificationOptionIds->isNotEmpty()) {
            foreach ($classificationOptionIds as $optionId) {
                $teamsQuery->whereHas('classificationOptions', function ($query) use ($optionId) {
                    $query->where('sport_classification_options.id', $optionId);
                });
            }
        }

        $teams = $teamsQuery->get()->map(fn ($t) => [
            'id' => $t->id,
            'name' => $t->name . ($t->club ? ' ('.$t->club->name.')' : ''),
            'division_id' => $t->division_id,
            'division_name' => optional($t->division)->name,
            'division_category' => optional($t->division)->category,
            'gender_id' => $t->gender_id,
            'age_group_id' => $t->age_group_id,
        ]);

        $eligibility = $this->getEligibilityData($sportId);

        return response()->json([
            'teams' => $teams,
            'genders' => $eligibility['genders'],
            'age_groups' => $eligibility['age_groups'],
            'classification_groups' => $eligibility['classification_groups'],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'               => 'required|string|max:255|unique:tournaments,name',
            'host_club_id'       => 'required|exists:clubs,id',
            'start_date'         => 'required|date|after_or_equal:today',
            'end_date'           => 'required|date|after_or_equal:start_date',
            'location'           => 'required|string|max:255',
            'country_id'         => 'required|exists:countries,id',
            'state_id'           => [
                'required',
                Rule::exists('states', 'id')->where(fn($query) => $query->where('country_id', $request->input('country_id'))),
            ],
            'city_id'            => [
                'required',
                Rule::exists('cities', 'id')->where(fn($query) => $query->where('state_id', $request->input('state_id'))),
            ],
            'tournament_format_id' => 'required|in:1,2,3',
            'venue_id'          => [
                'nullable',
                'integer',
                Rule::exists('venues', 'id')->where(function ($query) use ($request) {
                    $cityId = $request->integer('city_id');
                    if ($cityId) {
                        $query->where('city_id', $cityId);
                    }
                }),
            ],
            'description'        => 'nullable|string|max:1000',
            'gender_id'          => ['nullable', 'integer', 'exists:genders,id'],
            'age_group_id'       => ['nullable', 'integer', 'exists:age_groups,id'],
            'classification_option_ids'   => ['nullable', 'array'],
            'classification_option_ids.*' => ['nullable','integer', 'exists:sport_classification_options,id'],
            'team_ids'   => 'required|array|min:2',
            'team_ids.*' => 'exists:teams,id|distinct',
        ], [
            'name.unique' => 'A tournament with this name already exists.',
            'start_date.after_or_equal' => 'Start date must be today or a future date.',
            'end_date.after_or_equal' => 'End date must be after or equal to start date.',
            'team_ids.min' => 'At least 2 teams are required for a tournament.',
            'team_ids.*.distinct' => 'Each team can only be selected once.',
        ]);

        $hostClub = Club::findOrFail($validated['host_club_id']);

        $this->tournamentCreator->create($validated, $hostClub, [
            'clubLabel' => 'the host club',
        ]);

        return redirect()
            ->route('admin.tournaments.index')
            ->with('success', 'Tournament created successfully with ' . count($validated['team_ids']) . ' teams.');
    }



    public function edit(Tournament $tournament)
    {
        $clubs = Club::pluck('name', 'id');
        $teams = Team::pluck('name', 'id');
        $formats = ['1' => 'RoundRobin', '2' => 'Knockout', '3' => 'group'];

        $countries = Country::orderBy('name')->get(['id', 'name']);
        $selectedCountryId = old('country_id', $tournament->country_id);
        $states = $selectedCountryId
            ? State::where('country_id', $selectedCountryId)->orderBy('name')->get(['id', 'name'])
            : collect();

        $selectedStateId = old('state_id', $tournament->state_id);
        $cities = $selectedStateId
            ? City::where('state_id', $selectedStateId)->orderBy('name')->get(['id', 'name'])
            : collect();

        $selectedCityId = old('city_id', $tournament->city_id);
        $venues = $selectedCityId
            ? Venue::where('city_id', $selectedCityId)->orderBy('name')->get(['id', 'name', 'location'])
            : collect();

        $tournament->load(['teams', 'hostClub', 'venue', 'classificationOptions', 'gender', 'ageGroup']);

        $eligibilityData = $this->getEligibilityData(optional($tournament->hostClub)->sport_id);

        return view('admin.tournaments.edit', compact('tournament', 'clubs', 'formats', 'teams', 'countries', 'states', 'cities', 'venues', 'eligibilityData'));
    }


    public function update(Request $request, Tournament $tournament)
    {
        // ---------- 1. Validate everything we'll touch ----------
        $validated = $request->validate([
            'name'               => 'required|string|max:255|unique:tournaments,name,' . $tournament->id,
            'host_club_id'       => 'required|exists:clubs,id',
            'start_date'         => 'required|date|after_or_equal:today',
            'end_date'           => 'required|date|after_or_equal:start_date',
            'location'           => 'required|string|max:255',
            'country_id'         => 'required|exists:countries,id',
            'state_id'           => [
                'required',
                Rule::exists('states', 'id')->where(fn($query) => $query->where('country_id', $request->input('country_id'))),
            ],
            'city_id'            => [
                'required',
                Rule::exists('cities', 'id')->where(fn($query) => $query->where('state_id', $request->input('state_id'))),
            ],
            'tournament_format_id' => 'required|in:1,2,3',
            'venue_id'          => [
                'nullable',
                'integer',
                Rule::exists('venues', 'id')->where(function ($query) use ($request) {
                    $cityId = $request->integer('city_id');
                    if ($cityId) {
                        $query->where('city_id', $cityId);
                    }
                }),
            ],
            'description'        => 'nullable|string|max:1000',
            'gender_id'          => ['nullable', 'integer', 'exists:genders,id'],
            'age_group_id'       => ['nullable', 'integer', 'exists:age_groups,id'],
            'classification_option_ids'   => ['nullable', 'array'],
            'classification_option_ids.*' => ['integer', 'exists:sport_classification_options,id'],

            // teams (pivot)
            'team_ids'   => 'required|array|min:2',
            'team_ids.*' => 'exists:teams,id|distinct',
        ], [
            'name.unique' => 'A tournament with this name already exists.',
            'start_date.after_or_equal' => 'Start date must be today or a future date.',
            'end_date.after_or_equal' => 'End date must be after or equal to start date.',
            'team_ids.min' => 'At least 2 teams are required for a tournament.',
            'team_ids.*.distinct' => 'Each team can only be selected once.',
        ]);

        $hostClub = Club::findOrFail($validated['host_club_id']);

        $genderId = $validated['gender_id'] ?? null;
        $ageGroupId = $validated['age_group_id'] ?? null;
        $classificationOptionIds = collect($validated['classification_option_ids'] ?? [])
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if ($genderId && ! Gender::where('sport_id', $hostClub->sport_id)->where('id', $genderId)->exists()) {
            return back()->withErrors(['gender_id' => 'Selected gender is not available for the host club sport.'])->withInput();
        }

        if ($ageGroupId && ! AgeGroup::where('sport_id', $hostClub->sport_id)->where('id', $ageGroupId)->exists()) {
            return back()->withErrors(['age_group_id' => 'Selected age group is not available for the host club sport.'])->withInput();
        }

        if ($classificationOptionIds->isNotEmpty()) {
            $invalidOptionIds = SportClassificationOption::whereIn('id', $classificationOptionIds)
                ->whereDoesntHave('group', fn ($query) => $query->where('sport_id', $hostClub->sport_id))
                ->pluck('id');

            if ($invalidOptionIds->isNotEmpty()) {
                return back()->withErrors([
                    'classification_option_ids' => 'One or more sport options are not available for the host club sport.',
                ])->withInput();
            }
        }

        $teams = Team::whereIn('id', $validated['team_ids'])->get();
        $mismatch = $teams->first(fn ($team) => $team->sport_id !== $hostClub->sport_id);
        if ($mismatch) {
            return back()->withErrors(['team_ids' => 'All teams must match the host club sport.'])->withInput();
        }

        if ($genderId) {
            $genderMismatch = $teams->first(fn ($team) => (int) $team->gender_id !== (int) $genderId);
            if ($genderMismatch) {
                return back()->withErrors([
                    'team_ids' => 'Selected teams must match the chosen gender category.',
                ])->withInput();
            }
        }

        if ($ageGroupId) {
            $ageMismatch = $teams->first(fn ($team) => (int) $team->age_group_id !== (int) $ageGroupId);
            if ($ageMismatch) {
                return back()->withErrors([
                    'team_ids' => 'Selected teams must match the chosen age group.',
                ])->withInput();
            }
        }

        if ($classificationOptionIds->isNotEmpty()) {
            $teams->loadMissing('classificationOptions:id');
            $classificationMismatch = $teams->first(function ($team) use ($classificationOptionIds) {
                $teamOptionIds = $team->classificationOptions
                    ->pluck('id')
                    ->map(fn ($id) => (int) $id);

                return $classificationOptionIds->diff($teamOptionIds)->isNotEmpty();
            });

            if ($classificationMismatch) {
                return back()->withErrors([
                    'team_ids' => 'Selected teams must satisfy the chosen sport option filters.',
                ])->withInput();
            }
        }

        // ---------- 2. Validate tournament format requirements ----------
        $this->tournamentCreator->ensureValidFormat($validated['tournament_format_id'], $validated['team_ids']);

        // ---------- 3. Save tournament + sync teams atomically ----------
        DB::transaction(function () use ($tournament, $validated, $classificationOptionIds) {
            // Update the base record (strip out team_ids first)
            $tournament->update(Arr::except($validated, ['team_ids', 'classification_option_ids']));

            // Sync the pivot table (adds, keeps, and removes as needed)
            $tournament->teams()->sync($validated['team_ids']);
            $tournament->classificationOptions()->sync($classificationOptionIds->all());
        });

        return redirect()
            ->route('admin.tournaments.index')
            ->with('success', 'Tournament updated successfully.');
    }

    public function venuesForCity(Request $request)
    {
        $cityId = $request->integer('city_id');

        if (! $cityId) {
            return response()->json(['data' => []]);
        }

        $venues = Venue::where('city_id', $cityId)
            ->orderBy('name')
            ->get(['id', 'name', 'location']);

        return response()->json([
            'data' => $venues->map(fn ($venue) => [
                'id' => $venue->id,
                'name' => $venue->name,
                'location' => $venue->location,
            ]),
        ]);
    }

    private function getEligibilityData(?int $sportId): array
    {
        if (! $sportId) {
            return [
                'genders' => [],
                'age_groups' => [],
                'classification_groups' => [],
            ];
        }

        $genders = Gender::where('sport_id', $sportId)
            ->ordered()
            ->get(['id', 'label', 'code'])
            ->map(fn ($gender) => [
                'id' => $gender->id,
                'name' => $gender->label,
                'label' => $gender->label,
                'code' => $gender->code,
            ])->values()->all();

        $ageGroups = AgeGroup::where('sport_id', $sportId)
            ->ordered()
            ->get(['id', 'label', 'code', 'min_age_years', 'max_age_years', 'is_open_ended'])
            ->map(fn ($ageGroup) => [
                'id' => $ageGroup->id,
                'name' => $ageGroup->label,
                'label' => $ageGroup->label,
                'code' => $ageGroup->code,
                'min_age_years' => $ageGroup->min_age_years,
                'max_age_years' => $ageGroup->max_age_years,
                'is_open_ended' => $ageGroup->is_open_ended,
            ])->values()->all();

        $classificationGroups = SportClassificationGroup::where('sport_id', $sportId)
            ->ordered()
            ->with(['options' => fn ($query) => $query->ordered()->select('id', 'label', 'code', 'group_id')])
            ->get(['id', 'name', 'code', 'description'])
            ->map(fn ($group) => [
                'id' => $group->id,
                'name' => $group->name,
                'code' => $group->code,
                'description' => $group->description,
                'options' => $group->options->map(fn ($option) => [
                    'id' => $option->id,
                    'name' => $option->label,
                    'label' => $option->label,
                    'code' => $option->code,
                ])->values()->all(),
            ])->values()->all();

        return [
            'genders' => $genders,
            'age_groups' => $ageGroups,
            'classification_groups' => $classificationGroups,
        ];
    }


    public function destroy(Tournament $tournament)
    {
        $tournament->delete();
        return back()->with('success', 'Tournament deleted.');
    }

    public function assignRefereeToMatch(Request $request, GameMatch $match)
    {
        $request->validate([
            'referee_id' => 'required|exists:referees,id'
        ]);

        // Check if referee is qualified for this match
        $referee = \App\Models\Referee::find($request->referee_id);
        if (!$match->availableReferees()->where('referees.id', $referee->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'This referee is not qualified for this match based on expertise requirements.'
            ], 400);
        }

        $match->update(['referee_id' => $request->referee_id]);

        return response()->json([
            'success' => true,
            'message' => 'Referee assigned successfully!'
        ]);
    }

    public function removeRefereeFromMatch(GameMatch $match)
    {
        $match->update(['referee_id' => null]);

        return response()->json([
            'success' => true,
            'message' => 'Referee removed successfully!'
        ]);
    }
}
