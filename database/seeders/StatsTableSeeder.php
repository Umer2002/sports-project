<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;
use App\Models\Sport;

class StatsTableSeeder extends Seeder
{
    public function run(): void
    {
        // Shared fields for every sport
        $shared = [
            'Game ID',
            'Team IDs',
            'Location',
            'League/tournament ID',
            'Referee(s)',
            'Weather (for outdoor)',
            'Attendance (optional)',
            'Video link or highlight ID',
        ];

        // Stats by sport
        $statsBySport = [
            'Soccer' => [
                'Match date & time',
                'Final score',
                'Goal scorers',
                'Assists',
                'Yellow/red cards',
                'Saves (goalkeeper)',
                'Shots on/off target',
                'Player of the match',
                'Substitutions',
                'Possession %',
            ],
            'BASKETBALL' => [
                'Match date & time',
                'Final score',
                'Points per player',
                'Assists',
                'Rebounds (off/def)',
                'Steals',
                'Blocks',
                'Fouls',
                'Turnovers',
                'Field goal %',
                '3-point shots made',
                'Free throw %',
            ],
            'FOOTBALL' => [
                'Match date & time',
                'Final score',
                'Touchdowns (player)',
                'Passing yards',
                'Rushing yards',
                'Receiving yards',
                'Interceptions',
                'Tackles',
                'Field goals',
                'Extra points',
                'Fumbles (lost/recovered)',
                'Sacks',
            ],
            'BASEBALL' => [
                'Match date & time',
                'Final score',
                'Runs',
                'Hits',
                'Home runs',
                'RBIs',
                'Strikeouts',
                'Walks',
                'Stolen bases',
                'Errors',
                'Pitcher stats (ERA, K, BB)',
            ],
            'Volleyball' => [
                'Match date & time',
                'Final score (per set)',
                'Total sets won',
                'Aces',
                'Kills',
                'Blocks',
                'Digs',
                'Errors (serving, attacking)',
                'Assists',
            ],
            'HOCKEY' => [
                'Match date & time',
                'Final score',
                'Goals per player',
                'Assists',
                'Penalties',
                'Saves (goalie)',
                'Shots on goal',
                'Faceoff wins',
                'Hits',
                'Power-play goals',
            ],
            'Field hockey' => [
                'Match date & time',
                'Final score',
                'Goals',
                'Assists',
                'Cards (green/yellow/red)',
                'Penalty corners won',
                'Saves (goalie)',
            ],
            'Rugby' => [
                'Match date & time',
                'Final score',
                'Tries',
                'Conversions',
                'Penalty kicks',
                'Drop goals',
                'Tackles',
                'Turnovers',
                'Yellow/red cards',
                'Lineouts won',
                'Scrums won',
            ],
            'Boxing' => [
                'Match date & time',
                'Winner',
                'Method (KO, TKO, Decision)',
                'Rounds completed',
                'Knockdowns',
                'Punches landed/thrown',
                'Significant strikes',
            ],
            'Gymnastics' => [
                'Event name',
                'Date',
                'Athlete name',
                'Apparatus (vault, bars, beam, floor)',
                'Difficulty score',
                'Execution score',
                'Total score',
                'Final rank',
            ],
            'Track and Field' => [
                'Event name',
                'Date',
                'Type (100m, long jump, relay, etc.)',
                'Athlete name',
                'Result (time/distance/height)',
                'Final rank',
                'Personal best or not',
                'Wind speed',
            ],
            'Swimming' => [
                'Event name',
                'Date',
                'Distance/stroke',
                'Time',
                'Heat and lane',
                'Final rank',
                'Personal best or not',
                'Split times',
            ],
            'Tennis' => [
                'Match date',
                'Final score (set by set)',
                'Aces',
                'Double faults',
                'First serve %',
                'Winners',
                'Unforced errors',
                'Break points won/saved',
            ],
            'Golf' => [
                'Round date',
                'Course played',
                'Strokes per hole',
                'Total strokes',
                'Putts',
                'Fairways hit',
                'Greens in regulation',
                'Penalties',
                'Final score',
                'Rank for round/event',
            ],
            'Wrestling' => [
                'Match date',
                'Winner',
                'Method (Pin, Points, DQ)',
                'Points scored',
                'Rounds won',
                'Takedowns',
                'Escapes',
                'Reversals',
            ],
            'Mixed Martial Arts' => [
                'Match date',
                'Winner',
                'Method (KO, Submission, Decision)',
                'Round ended',
                'Total strikes landed',
                'Takedowns',
                'Submission attempts',
                'Control time',
            ],
            'Lacrosse' => [
                'Match date',
                'Final score',
                'Goals per player',
                'Assists',
                'Saves (goalie)',
                'Ground balls',
                'Turnovers',
                'Penalties',
            ],
        ];

        foreach ($statsBySport as $sportName => $stats) {
            $sport = Sport::where('name', $sportName)->first();
            if (!$sport) {
                continue;
            }
            $allStats = array_merge($stats, $shared);
            foreach ($allStats as $statName) {
                DB::table('stats')->updateOrInsert(
                    ['name' => $statName, 'sports_id' => $sport->id],
                    ['created_at' => Carbon::now(), 'updated_at' => Carbon::now()]
                );
            }
        }
    }
}

