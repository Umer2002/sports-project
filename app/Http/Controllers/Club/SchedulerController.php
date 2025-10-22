<?php

namespace App\Http\Controllers\Club;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\Venue;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SchedulerController extends Controller
{
    public function generate(Tournament $tournament)
    {
        $club = optional(auth()->user())->club;
        $this->ensureTournamentBelongsToClub($tournament, $club?->id);
        $this->ensureSchedulingWindowOpen($tournament);

        $events = Event::where('group_id', $tournament->id)
            ->orderBy('event_date')
            ->get();

        $matches = $events->map(function (Event $event) {
            $title = trim(preg_replace('/\s+/', ' ', $event->title ?? ''));

            if ($title === '' || stripos($title, 'vs') === false) {
                return null;
            }

            $parts = preg_split('/\s+vs\s+/i', $title, 2);
            if (! $parts || count($parts) !== 2) {
                return null;
            }

            [$homeName, $awayName] = array_map('trim', $parts);

            $homeTeam = Team::where('name', $homeName)->first();
            $awayTeam = Team::where('name', $awayName)->first();
            if (! $homeTeam || ! $awayTeam) {
                return null;
            }

            $venue = Venue::where('name', $event->location)->first();

            return [
                'home'       => $homeTeam,
                'away'       => $awayTeam,
                'match_date' => $event->event_date ?? optional($event->start)->toDateString(),
                'match_time' => $event->event_time ?? optional($event->start)->format('H:i'),
                'venue_id'   => optional($venue)->id,
                'venue_name' => $venue->name ?? $event->location,
            ];
        })->filter();

        $venues = Venue::query()
            ->orderBy('name')
            ->where(function ($query) use ($club) {
                $query->whereNull('club_id');
                if ($club) {
                    $query->orWhere('club_id', $club->id);
                }
            })
            ->get();

        return view('club.tournaments.schedule', [
            'tournament' => $tournament,
            'matches'    => $matches,
            'venues'     => $venues,
        ]);
    }

    public function store(Request $request, Tournament $tournament)
    {
        $club = optional(auth()->user())->club;
        $this->ensureTournamentBelongsToClub($tournament, $club?->id);
        $this->ensureSchedulingWindowOpen($tournament);

        $validated = $request->validate([
            'matches'                        => 'required|array',
            'matches.*.home_club_id'         => 'required|exists:teams,id',
            'matches.*.away_club_id'         => 'required|exists:teams,id',
            'matches.*.match_date'           => 'required|date',
            'matches.*.match_time'           => [
                'required',
                function ($attribute, $value, $fail) {
                    if (! preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $value)) {
                        $fail('Match time must be in the format HH:MM or HH:MM:SS.');
                    }
                },
            ],
            'matches.*.venue_id'             => 'required|exists:venues,id',
        ]);

        $validTeamIds = $tournament->teams()->pluck('teams.id')->all();
        foreach ($validated['matches'] as $key => $match) {
            if (! in_array((int) $match['home_club_id'], $validTeamIds, true) ||
                ! in_array((int) $match['away_club_id'], $validTeamIds, true)) {
                return back()
                    ->withErrors(['matches' => 'One or more selected teams are not part of this tournament.'])
                    ->withInput();
            }
        }

        foreach ($validated['matches'] as $match) {
            $homeTeam = Team::find($match['home_club_id']);
            $awayTeam = Team::find($match['away_club_id']);
            $venue    = Venue::find($match['venue_id']);

            if (! $homeTeam || ! $awayTeam || ! $venue) {
                continue;
            }

            $title = sprintf('%s vs %s', $homeTeam->name, $awayTeam->name);
            $time = Carbon::parse($match['match_time'])->format('H:i:s');
            $dateTime = sprintf('%s %s', $match['match_date'], $time);

            $event = Event::where('group_id', $tournament->id)
                ->where('title', $title)
                ->first();

            if ($event) {
                $event->update([
                    'event_date' => $match['match_date'],
                    'event_time' => $time,
                    'start'      => $dateTime,
                    'location'   => $venue->name,
                ]);
            }
        }

        return redirect()
            ->route('club.tournaments.show', $tournament)
            ->with('success', 'Match schedule updated successfully.');
    }

    private function ensureTournamentBelongsToClub(Tournament $tournament, ?int $clubId): void
    {
        if (! $clubId || (int) $tournament->host_club_id !== (int) $clubId) {
            abort(403, 'You do not have access to this tournament.');
        }
    }

    private function ensureSchedulingWindowOpen(Tournament $tournament): void
    {
        if ($tournament->registration_cutoff_date && now()->lt($tournament->registration_cutoff_date)) {
            abort(403, 'Scheduling opens after the registration cutoff date.');
        }
    }
}
