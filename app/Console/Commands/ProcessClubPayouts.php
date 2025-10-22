<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Club;
use App\Models\Payment;

class ProcessClubPayouts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clubs:process-payouts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process payouts for clubs that are eligible for final payout calculation';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Processing club payouts...');

        $clubs = Club::all();
        $processedCount = 0;

        foreach ($clubs as $club) {
            // Process initial player count (after 2 weeks)
            if ($club->processInitialPlayerCount()) {
                $this->info("Processed initial player count for club: {$club->name} ({$club->initial_player_count} players)");
            }

            // Process final payout (after 89 days)
            if ($club->processFinalPayout()) {
                $this->info("Processed final payout for club: {$club->name} (${$club->final_payout})");
                $processedCount++;
            }
        }

        $this->info("Completed processing payouts. {$processedCount} clubs had final payouts calculated.");

        return 0;
    }
} 