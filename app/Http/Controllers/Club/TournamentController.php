<?php

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use App\Models\Tournament;
use App\Models\Club;
use Illuminate\Http\Request;
use App\Models\Team;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use Illuminate\Validation\Rule;
use App\Models\Gender;
use App\Models\AgeGroup;
use App\Models\SportClassificationGroup;
use App\Models\SportClassificationOption;
use App\Models\Venue;
use App\Models\Event;
use App\Models\GameMatch;
use App\Services\Tournament\TournamentCreationService;

class TournamentController extends Controller
{
    public function __construct(private TournamentCreationService $tournamentCreator)
    {
    }

    public function index()
    {
        // Get tournaments that belong to the current club or are hosted by the club
        $club = auth()->user()->club;
        $tournaments = Tournament::where('host_club_id', $club->id)
            ->with(['hostClub'])
            ->paginate(10);

        return view('club.tournaments.index', compact('tournaments'));
    }

    public function show(Tournament $tournament)
    {
        // Ensure tournament belongs to current club
        $this->authorizeClubTournament($tournament);

        $tournament->load([
            'hostClub',
            'teams',
            'scheduledGames.referee',
            'scheduledGames.homeClub',
            'scheduledGames.awayClub',
            'invites.registeredClub',
            'invites.inviter',
            'registrations.club',
            'registrations.invite',
        ]);

        $matches = GameMatch::where('tournament_id', $tournament->id)
            ->with(['homeClub', 'awayClub', 'referee', 'applications.referee.user'])
            ->orderBy('match_date')
            ->orderBy('match_time')
            ->get();

        $scheduledMatches = Event::where('group_id', $tournament->id)
            ->orderBy('event_date')
            ->orderBy('event_time')
            ->get()
            ->map(function (Event $event) {
                $title = trim(preg_replace('/\s+/', ' ', $event->title ?? ''));

                if ($title === '' || stripos($title, 'vs') === false) {
                    return null;
                }

                $parts = preg_split('/\s+vs\s+/i', $title, 2);
                if (! $parts || count($parts) !== 2) {
                    return null;
                }

                [$homeName, $awayName] = array_map('trim', $parts);

                return [
                    'home_name' => $homeName,
                    'away_name' => $awayName,
                    'match_date' => $event->event_date ?? optional($event->start)->toDateString(),
                    'match_time' => $event->event_time ?? optional($event->start)->format('H:i'),
                    'venue_name' => $this->resolveVenueName($event->location),
                ];
            })
            ->filter()
            ->values();

        $invites = $tournament->invites->sortByDesc('created_at');
        $registrations = $tournament->registrations->sortByDesc('created_at');

        return view('club.tournaments.show', compact('tournament', 'matches', 'scheduledMatches', 'invites', 'registrations'));
    }

    public function create(Request $request)
    {
        $club = auth()->user()->club;
        // Teams limited to the current club
        $teams = $club->teams()->orderBy('name')->get();
        // Use same format as admin
        $formats = ['1'=>'RoundRobin','2'=>'Knockout','3'=>'group'];

        $countries = Country::orderBy('name')->get(['id', 'name']);

        $selectedCountryId = $request->old('country_id');
        $states = $selectedCountryId
            ? State::where('country_id', $selectedCountryId)->orderBy('name')->get(['id', 'name'])
            : collect();

        $selectedStateId = $request->old('state_id');
        $cities = $selectedStateId
            ? City::where('state_id', $selectedStateId)->orderBy('name')->get(['id', 'name'])
            : collect();

        $selectedCityId = $request->old('city_id');
        $venues = $selectedCityId
            ? Venue::where('club_id', $club->id)
                ->where('city_id', $selectedCityId)
                ->orderBy('name')
                ->get(['id', 'name', 'location'])
            : collect();

        $eligibilityData = $this->getEligibilityData($club->sport_id);

        return view('club.tournaments.create', compact('formats', 'teams', 'club', 'countries', 'states', 'cities', 'venues', 'eligibilityData'));
    }

    /**
     * Return teams that match the host club's sport (JSON)
     */
    public function teamsForHostClub(Request $request, Club $club)
    {
        // Ensure the club belongs to the current user
        if ($club->id !== auth()->user()->club->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

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

        $teamsQuery = Team::where('club_id', $club->id)
            ->where('sport_id', $sportId)
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
        ]);
        $eligibility = $this->getEligibilityData($sportId);

        return response()->json([
            'teams' => $teams,
            'genders' => $eligibility['genders'],
            'age_groups' => $eligibility['age_groups'],
            'classification_groups' => $eligibility['classification_groups'],
        ]);
    }

    public function venuesForCity(Request $request)
    {
        $club = auth()->user()->club;
        $cityId = $request->integer('city_id');

        if (! $cityId) {
            return response()->json(['data' => []]);
        }

        $venues = Venue::where('city_id', $cityId)
            ->where(function ($query) use ($club) {
                $query->whereNull('club_id')
                    ->orWhere('club_id', $club->id);
            })
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

    private function resolveVenueName($location): ?string
    {
        if (! $location) {
            return null;
        }

        if (is_numeric($location)) {
            return Venue::whereKey((int) $location)->value('name');
        }

        return $location;
    }

    public function store(Request $request)
    {
        $club = auth()->user()->club;

        $validated = $request->validate([
            'name'               => 'required|string|max:255|unique:tournaments,name',
            // 'division_id'        => 'required|exists:divisions,id',
            'description'        => 'nullable|string|max:1000',
            'start_date'         => 'required|date|after_or_equal:today',
            'end_date'           => 'required|date|after_or_equal:start_date',
            'registration_cutoff_date' => 'required|date|after_or_equal:today|before_or_equal:start_date',
            'location'           => 'required|string|max:255',
            'country_id'         => 'required|exists:countries,id',
            'state_id'           => [
                'required',
                Rule::exists('states', 'id')->where(fn ($query) => $query->where('country_id', $request->input('country_id'))),
            ],
            'city_id'            => [
                'required',
                Rule::exists('cities', 'id')->where(fn ($query) => $query->where('state_id', $request->input('state_id'))),
            ],
            'tournament_format_id' => 'required|in:1,2,3',
            'team_ids'   => 'required|array|min:2',
            'team_ids.*' => 'exists:teams,id|distinct',
            'gender_id'          => ['nullable', 'integer', 'exists:genders,id'],
            'age_group_id'       => ['nullable', 'integer', 'exists:age_groups,id'],
            'classification_option_ids'   => ['nullable', 'array'],
            'classification_option_ids.*' => ['nullable','integer', 'exists:sport_classification_options,id'],
            'joining_fee'        => 'required|numeric|min:0',
            'joining_type'       => 'required|in:per_club,per_team',
        ], [
            'name.unique' => 'A tournament with this name already exists.',
            'start_date.after_or_equal' => 'Start date must be today or a future date.',
            'end_date.after_or_equal' => 'End date must be after or equal to start date.',
            'registration_cutoff_date.before_or_equal' => 'Cutoff date must be on or before the tournament start date.',
            'team_ids.min' => 'At least 2 teams are required for a tournament.',
            'team_ids.*.distinct' => 'Each team can only be selected once.',
        ]);

        $validated['host_club_id'] = $club->id;

        $this->tournamentCreator->create($validated, $club, [
            'restrictTeamsToHostClub' => true,
            'clubLabel' => 'your club',
            'createFallbackVenue' => true,
        ]);

        return redirect()
            ->route('club.tournaments.index')
            ->with('success', 'Tournament created successfully with ' . count($validated['team_ids']) . ' teams.');
    }

    /**
     * Store tournament from modal form with error handling
     */
    public function storeFromModal(Request $request)
    {
        $club = auth()->user()->club;

        try {
            $validated = $request->validate([
                'name'               => 'required|string|max:255|unique:tournaments,name',
                'description'        => 'nullable|string|max:1000',
                'start_date'         => 'required|date|after_or_equal:today',
                'end_date'           => 'required|date|after_or_equal:start_date',
                'registration_cutoff_date' => 'required|date|after_or_equal:today|before_or_equal:start_date',
                'location'           => 'required|string|max:255',
                'country_id'         => 'required|exists:countries,id',
                'state_id'           => [
                    'required',
                    Rule::exists('states', 'id')->where(fn ($query) => $query->where('country_id', $request->input('country_id'))),
                ],
                'city_id'            => [
                    'required',
                    Rule::exists('cities', 'id')->where(fn ($query) => $query->where('state_id', $request->input('state_id'))),
                ],
                'tournament_format_id' => 'required|in:1,2,3',
                'team_ids'   => 'required|array|min:2',
                'team_ids.*' => 'exists:teams,id|distinct',
                'gender_id'          => ['nullable', 'integer', 'exists:genders,id'],
                'age_group_id'       => ['nullable', 'integer', 'exists:age_groups,id'],
                'classification_option_ids'   => ['nullable', 'array'],
                'classification_option_ids.*' => ['nullable','integer', 'exists:sport_classification_options,id'],
                'joining_fee'        => 'required|numeric|min:0',
                'joining_type'       => 'required|in:per_club,per_team',
                'sport_id'           => 'required|exists:sports,id',
            ], [
                'name.unique' => 'A tournament with this name already exists.',
                'start_date.after_or_equal' => 'Start date must be today or a future date.',
                'end_date.after_or_equal' => 'End date must be after or equal to start date.',
                'registration_cutoff_date.before_or_equal' => 'Cutoff date must be on or before the tournament start date.',
                'team_ids.min' => 'At least 2 teams are required for a tournament.',
                'team_ids.*.distinct' => 'Each team can only be selected once.',
            ]);

            $validated['host_club_id'] = $club->id;

            $this->tournamentCreator->create($validated, $club, [
                'restrictTeamsToHostClub' => true,
                'clubLabel' => 'your club',
                'createFallbackVenue' => true,
            ]);

            return redirect()
                ->route('club.tournaments.index')
                ->with('success', 'Tournament created successfully with ' . count($validated['team_ids']) . ' teams.');

        } catch (\Illuminate\Validation\ValidationException $e) {
            // If validation fails, redirect to create page with errors
            return redirect()
                ->route('club.tournaments.create')
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            // For other errors, also redirect to create page
            return redirect()
                ->route('club.tournaments.create')
                ->withErrors(['error' => 'An error occurred while creating the tournament: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function edit(Tournament $tournament)
    {
        // Ensure tournament belongs to current club
        $this->authorizeClubTournament($tournament);

        $club = auth()->user()->club;
        $teams = Team::pluck('name', 'id');
        // Use same format as create method
        $formats = ['1'=>'RoundRobin','2'=>'Knockout','3'=>'group'];

        // Load tournament with relationships for better data access
        $tournament->load(['teams', 'hostClub']);

        return view('club.tournaments.edit', compact('tournament', 'formats', 'teams', 'club'));
    }

    public function update(Request $request, Tournament $tournament)
    {
        // Ensure tournament belongs to current club
        $this->authorizeClubTournament($tournament);

        // ---------- 1. Validate everything we'll touch ----------
        $validated = $request->validate([
            'name'               => 'required|string|max:255|unique:tournaments,name,' . $tournament->id,
            'start_date'         => 'required|date|after_or_equal:today',
            'end_date'           => 'required|date|after_or_equal:start_date',
            'registration_cutoff_date' => 'required|date|after_or_equal:today|before_or_equal:start_date',
            'location'           => 'required|string|max:255',
            'country_id'         => 'nullable|exists:countries,id',
            'state_id'           => [
                'nullable',
                Rule::exists('states', 'id')->where(fn ($query) => $query->where('country_id', $request->input('country_id'))),
            ],
            'city_id'            => [
                'nullable',
                Rule::exists('cities', 'id')->where(fn ($query) => $query->where('state_id', $request->input('state_id'))),
            ],
            'tournament_format_id' => 'required|in:1,2,3',
            'division_id'        => 'required|exists:divisions,id',
            'description'        => 'nullable|string|max:1000',

            'team_ids'   => 'required|array|min:2',
            'team_ids.*' => 'exists:teams,id|distinct',
            'joining_fee'        => 'required|numeric|min:0',
            'joining_type'       => 'required|in:per_club,per_team',
        ], [
            'name.unique' => 'A tournament with this name already exists.',
            'start_date.after_or_equal' => 'Start date must be today or a future date.',
            'end_date.after_or_equal' => 'End date must be after or equal to start date.',
            'registration_cutoff_date.before_or_equal' => 'Cutoff date must be on or before the tournament start date.',
            'team_ids.min' => 'At least 2 teams are required for a tournament.',
            'team_ids.*.distinct' => 'Each team can only be selected once.',
        ]);

        // ---------- 2. Validate tournament format requirements ----------
        $this->tournamentCreator->ensureValidFormat($validated['tournament_format_id'], $validated['team_ids']);

        // ---------- 3. Save tournament + sync teams atomically ----------
        DB::transaction(function () use ($tournament, $validated) {
            // Update the base record (strip out team_ids first)
            $tournament->update(Arr::except($validated, ['team_ids']));

            // Sync the pivot table (adds, keeps, and removes as needed)
            $tournament->teams()->sync($validated['team_ids']);
        });

        return redirect()
            ->route('club.tournaments.index')
            ->with('success', 'Tournament updated successfully.');
    }

    public function destroy(Tournament $tournament)
    {
        // Ensure tournament belongs to current club
        $this->authorizeClubTournament($tournament);

        $tournament->delete();
        return back()->with('success', 'Tournament deleted.');
    }

    public function assignRefereeToMatch(Request $request, GameMatch $match)
    {
        // Ensure match belongs to a tournament hosted by current club
        if ($match->tournament->host_club_id !== auth()->user()->club->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

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
        // Ensure match belongs to a tournament hosted by current club
        if ($match->tournament->host_club_id !== auth()->user()->club->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $match->update(['referee_id' => null]);

        return response()->json([
            'success' => true,
            'message' => 'Referee removed successfully!'
        ]);
    }

    /**
     * Ensure tournament belongs to current club
     */
    private function authorizeClubTournament(Tournament $tournament)
    {
        if ($tournament->host_club_id !== auth()->user()->club->id) {
            abort(403, 'Unauthorized access to tournament.');
        }
    }
}
