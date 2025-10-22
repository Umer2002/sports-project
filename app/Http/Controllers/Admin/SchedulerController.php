<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tournament;
use App\Models\Club;
use App\Models\Event;
use App\Models\Game;
use App\Models\Team;
use App\Models\Venue;
use App\Models\VenueAvailability;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


class SchedulerController extends Controller
{
    public function generate(Tournament $tournament)
    {
        /* ---------------------------------------------
        | 1. Pull all tournament-match events
        * --------------------------------------------*/
        $events = Event::where('group_id', $tournament->id)
            // ->where('type', 'tournament_match')
            ->orderBy('event_date')                 // keep them chronologically
            ->get();

        /* ---------------------------------------------
        | 2. Transform into the view-friendly structure
        * --------------------------------------------*/
        $matches = $events->map(function (Event $event) {
            $homeName = $awayName = null;

            // Cleaned event title
            $title = trim(preg_replace('/\s+/', ' ', $event->title)); // Normalize spaces

            if (stripos($title, 'vs') !== false) {
                $parts = preg_split('/\s+vs\s+/i', $title, 2);

                if (count($parts) === 2) {
                    [$homeName, $awayName] = array_map('trim', $parts);
                }
            }
            // --- B. Look up the clubs & venue ---
            $homeClub = Team::where('name', $homeName)->first();
            $awayClub = Team::where('name', $awayName)->first();
            $venue    = Venue::where('name', $event->location)->first();

            // --- C. Build the match payload ---
            return [
                'home'        => $homeClub,                      // used as $match['home']->name / ->id
                'away'        => $awayClub,                      // used as $match['away']->name / ->id
                'match_date'  => $event->event_date
                    ?? optional($event->start)->toDateString(),
                'match_time'  => $event->event_time
                    ?? optional($event->start)->format('H:i'),
                'venue_id'    => optional($venue)->id,
                'venue_name'  => $venue->name ?? $event->location,
            ];
        });

        /* ---------------------------------------------
        | 3. Return the view
        * --------------------------------------------*/
        return view('admin.scheduler.preview', [
            'tournament' => $tournament,
            'matches'    => $matches,
            'venues'     => \App\Models\Venue::all(),
        ]);
    }
    // public function generate(Tournament $tournament)
    // {
    //     $clubs = Club::all();
    //     $venues = Venue::with('availabilities')->get();

    //     $matches = [];
    //     $availableSlots = [];

    //     foreach ($venues as $venue) {
    //         foreach ($venue->availabilities as $slot) {
    //             $availableSlots[] = [
    //                 'venue_id' => $venue->id,
    //                 'venue_name' => $venue->name,
    //                 'match_date' => $slot->available_date,
    //                 'match_time' => $slot->start_time,
    //             ];
    //         }
    //     }

    //     $slotIndex = 0;
    //     for ($i = 0; $i < count($clubs); $i++) {
    //         for ($j = $i + 1; $j < count($clubs); $j++) {
    //             if (!isset($availableSlots[$slotIndex])) {
    //                 break 2; // Not enough slots
    //             }

    //             $slot = $availableSlots[$slotIndex++];
    //             $matches[] = [
    //                 'home' => $clubs[$i],
    //                 'away' => $clubs[$j],
    //                 'match_date' => $slot['match_date'],
    //                 'match_time' => $slot['match_time'],
    //                 'venue_id' => $slot['venue_id'],
    //                 'venue_name' => $slot['venue_name'],
    //             ];
    //         }
    //     }

    //     return view('admin.scheduler.preview', compact('tournament', 'matches'));
    // }

    // public function store(Request $request, Tournament $tournament)
    // {
    //     $request->validate([
    //         'matches' => 'required|array',
    //         'matches.*.home_club_id' => 'required|exists:clubs,id',
    //         'matches.*.away_club_id' => 'required|exists:clubs,id|different:matches.*.home_club_id',
    //         'matches.*.match_date' => 'required|date',
    //         'matches.*.match_time' => 'required|date_format:H:i',
    //         'matches.*.venue_id' => 'required|exists:venues,id',
    //     ]);

    //     foreach ($request->matches as $match) {
    //         Game::create([
    //             'tournament_id' => $tournament->id,
    //             'home_club_id' => $match['home_club_id'],
    //             'away_club_id' => $match['away_club_id'],
    //             'match_date' => $match['match_date'],
    //             'match_time' => $match['match_time'],
    //             'venue' => Venue::find($match['venue_id'])->name,
    //         ]);
    //     }

    //     return redirect()->route('admin.tournaments.show', $tournament)->with('success', 'Schedule saved with venue assignments.');
    // }

    public function store(Request $request, Tournament $tournament)
    {
        $validated = $request->validate([
            'matches'                         => 'required|array',
            'matches.*.home_club_id'          => 'required|exists:teams,id',
            'matches.*.away_club_id'          => 'required|exists:teams,id',
            'matches.*.match_date'            => 'required|date',
            'matches.*.match_time'            => [
                'required',
                function ($attribute, $value, $fail) {
                    // Accept "H:i" or "H:i:s"
                    if (!preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $value)) {
                        $fail("The {$attribute} must be a valid time (H:i or H:i:s).");
                    }
                },
            ],
            'matches.*.venue_id'              => 'required|exists:venues,id',
        ]);

        foreach ($validated['matches'] as $match) {
            $homeTeam = \App\Models\Team::find($match['home_club_id']);
            $awayTeam = \App\Models\Team::find($match['away_club_id']);
            $venue    = \App\Models\Venue::find($match['venue_id']);

            $title = "{$homeTeam->name} vs {$awayTeam->name}";

            // Normalize time to H:i:s
            $time = \Carbon\Carbon::parse($match['match_time'])->format('H:i:s');
            $dateTime = "{$match['match_date']} {$time}";

            $event = \App\Models\Event::where('group_id', $tournament->id)
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
            ->route('admin.tournaments.index')
            ->with('success', 'Match schedule updated successfully.');
    }


}
