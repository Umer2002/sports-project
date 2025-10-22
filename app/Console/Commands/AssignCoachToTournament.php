<?php

namespace App\Console\Commands;

use App\Models\Coach;
use App\Models\Tournament;
use Illuminate\Console\Command;

class AssignCoachToTournament extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'coach:assign-tournament {coach_id} {tournament_id} {role=coach}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign a coach to a tournament with a specific role';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $coachId = $this->argument('coach_id');
        $tournamentId = $this->argument('tournament_id');
        $role = $this->argument('role');

        $coach = Coach::find($coachId);
        $tournament = Tournament::find($tournamentId);

        if (!$coach) {
            $this->error("Coach with ID {$coachId} not found.");
            return 1;
        }

        if (!$tournament) {
            $this->error("Tournament with ID {$tournamentId} not found.");
            return 1;
        }

        // Assign coach to tournament
        $coach->tournaments()->syncWithoutDetaching([
            $tournament->id => ['role' => $role]
        ]);

        $this->info("Coach '{$coach->first_name} {$coach->last_name}' assigned to tournament '{$tournament->name}' as {$role}.");
        
        return 0;
    }
}
