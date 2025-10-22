<?php

/**
 * This controller and Blade structure provides a streamlined 4-step interactive wizard for team creation:
 * 1. Create Team (name, club, sport, division, logo)
 * 2. Define Eligibility (sport options, age group, gender)
 * 3. Select Players
 * 4. Drag-and-Drop Formation Builder
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Concerns\HandlesTeamEligibility;
use App\Models\AgeGroup;
use App\Models\Club;
use App\Models\Gender;
use App\Models\Player;
use App\Models\Position;
use App\Models\Sport;
use App\Models\SportClassificationGroup;
use App\Models\SportClassificationOption;
use App\Models\Team;
use Illuminate\Http\Request;

class TeamWizardController extends Controller
{
    use HandlesTeamEligibility;

    public function step1Form()
    {
        $clubs = Club::orderBy('name')->get(['id', 'name', 'sport_id']);

        $clubOptions = $clubs->pluck('name', 'id');
        $clubSports = $clubs->pluck('sport_id', 'id')->toArray();
        $sports = Sport::orderBy('name')->pluck('name', 'id');

        return view('admin.teams.wizard.step1', [
            'clubs' => $clubOptions,
            'clubSports' => $clubSports,
            'sports' => $sports,
        ]);
    }

    public function storeStep1(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'club_id' => 'required|exists:clubs,id',
            'sport_id' => 'nullable|exists:sports,id',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Force sport_id from the selected club (prevents tampering and avoids missing sport)
        $club = Club::findOrFail($data['club_id']);
        $data['sport_id'] = $club->sport_id;

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('team_logos', 'public');
        }

        $team = Team::create($data);

        return redirect()->route('admin.teams.wizard.step2', $team);
    }

    public function step2Form(Team $team)
    {
        $team->load(['sport', 'ageGroup', 'genderCategory', 'classificationOptions.group']);

        $ageGroups = AgeGroup::where('sport_id', $team->sport_id)->ordered()->get();
        $genders = Gender::where('sport_id', $team->sport_id)->ordered()->get();
        $classificationGroups = SportClassificationGroup::with(['options' => fn ($query) => $query->ordered()])
            ->where('sport_id', $team->sport_id)
            ->ordered()
            ->get();
        $selectedOptionIds = $team->classificationOptions->pluck('id')->all();

        return view('admin.teams.wizard.step2', [
            'team' => $team,
            'ageGroups' => $ageGroups,
            'genders' => $genders,
            'classificationGroups' => $classificationGroups,
            'selectedOptionIds' => $selectedOptionIds,
        ]);
    }

    public function storeStep2(Request $request, Team $team)
    {
        $payload = $request->validate([
            'age_group_id' => ['nullable', 'integer', 'exists:age_groups,id'],
            'gender_id' => ['nullable', 'integer', 'exists:genders,id'],
            'classification_option_ids' => ['nullable', 'array'],
            'classification_option_ids.*' => ['integer', 'exists:sport_classification_options,id'],
        ]);

        $ageGroupId = $payload['age_group_id'] ?? null;
        $genderId = $payload['gender_id'] ?? null;
        $optionIds = collect($payload['classification_option_ids'] ?? [])->filter()->map(fn ($id) => (int) $id)->values();

        if ($ageGroupId && ! AgeGroup::where('sport_id', $team->sport_id)->where('id', $ageGroupId)->exists()) {
            return back()->withErrors(['age_group_id' => 'Selected age group is not available for this sport.'])->withInput();
        }

        if ($genderId && ! Gender::where('sport_id', $team->sport_id)->where('id', $genderId)->exists()) {
            return back()->withErrors(['gender_id' => 'Selected gender is not available for this sport.'])->withInput();
        }

        $invalidOptionIds = SportClassificationOption::whereIn('id', $optionIds)
            ->whereDoesntHave('group', fn ($query) => $query->where('sport_id', $team->sport_id))
            ->pluck('id');

        if ($invalidOptionIds->isNotEmpty()) {
            return back()->withErrors(['classification_option_ids' => 'One or more sport options are not available for this sport.'])->withInput();
        }

        $team->update([
            'age_group_id' => $ageGroupId,
            'gender_id' => $genderId,
        ]);

        $team->classificationOptions()->sync($optionIds->all());

        return redirect()->route('admin.teams.wizard.step3', $team)->with('success', 'Eligibility preferences saved.');
    }

    public function step3Form(Team $team)
    {
        $team->load(['sport', 'players', 'ageGroup', 'genderCategory']);

        $availablePlayers = $this->filterPlayersForTeam(
            Player::where('sport_id', $team->sport_id)->orderBy('name')->get(),
            $team
        );

        $selectedPlayerIds = $team->players->pluck('id')->all();

        return view('admin.teams.wizard.step3', [
            'team' => $team,
            'availablePlayers' => $availablePlayers,
            'selectedPlayerIds' => $selectedPlayerIds,
        ]);
    }

    public function storePlayers(Request $request, Team $team)
    {
        $payload = $request->validate([
            'player_ids' => 'nullable|array',
            'player_ids.*' => 'exists:players,id',
        ]);

        $playerIds = $payload['player_ids'] ?? [];

        $syncData = [];
        foreach ($playerIds as $playerId) {
            $syncData[$playerId] = [
                'sport_id' => $team->sport_id,
                'position_id' => null,
            ];
        }

        $team->players()->sync($syncData);

        return redirect()->route('admin.teams.wizard.step4', $team)->with('success', 'Players updated successfully.');
    }

    public function step4Form(Team $team)
    {
        $team->load(['sport', 'players' => function ($query) {
            $query->with('position');
        }]);

        $positions = Position::where('sports_id', $team->sport_id)
            ->orderBy('position_name')
            ->get();

        return view('admin.teams.wizard.step4', compact('team', 'positions'));
    }

    public function finalizeFormation(Request $request, Team $team)
    {
        $playersData = json_decode($request->players ?? '{}', true);
        $entries = $playersData['players'] ?? [];

        $validated = validator(
            ['players' => $entries],
            [
                'players' => 'array',
                'players.*.id' => 'required|exists:players,id',
                'players.*.position_id' => 'nullable|exists:positions,id',
            ]
        )->validate();

        $syncData = [];
        foreach ($validated['players'] ?? [] as $data) {
            $syncData[$data['id']] = [
                'sport_id' => $team->sport_id,
                'position_id' => $data['position_id'] ?? null,
            ];
        }

        if (!empty($syncData)) {
            $team->players()->sync($syncData);
        }

        return redirect()->route('admin.teams.index')->with('success', 'Team formation finalized.');
    }
}
