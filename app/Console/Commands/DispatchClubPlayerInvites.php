<?php

namespace App\Console\Commands;

use App\Mail\PlayerInvitation;
use App\Models\Club;
use App\Models\Invite;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;

class DispatchClubPlayerInvites extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invites:dispatch-club-player {--limit=50 : Maximum invitations to send in this run}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deliver queued club player invitation emails in controlled batches';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = max(1, (int) $this->option('limit'));

        $pending = Invite::query()
            ->whereIn('type', ['club_invite', 'club'])
            ->whereNull('email_sent_at')
            ->orderBy('created_at')
            ->limit($limit)
            ->get();

        if ($pending->isEmpty()) {
            $this->info('No pending club player invitations to deliver.');
            return Command::SUCCESS;
        }

        $sent = 0;
        $skipped = 0;

        foreach ($pending as $invite) {
            $club = $invite->reference_id ? Club::find($invite->reference_id) : null;

            if (! $club || empty($invite->receiver_email)) {
                $invite->forceFill([
                    'email_last_attempt_at' => now(),
                    'email_attempts' => ($invite->email_attempts ?? 0) + 1,
                ])->save();
                $skipped++;
                continue;
            }

            $registerLink = $invite->getClubPlayerRegistrationLink();
            if (! $registerLink) {
                $registerLink = route('register.player', [
                    'club' => $club->id,
                    'sport' => optional($club->sport)->id,
                    'invite_token' => $invite->generateToken(),
                ]);
            }

            $personalMessage = Arr::get($invite->metadata, 'personal_message');
            $playerName = Arr::get($invite->metadata, 'player_name');

            try {
                Mail::to($invite->receiver_email)->queue(
                    new PlayerInvitation($club, $personalMessage, $playerName, $registerLink)
                );

                $invite->forceFill([
                    'email_sent_at' => now(),
                    'email_last_attempt_at' => now(),
                    'email_attempts' => ($invite->email_attempts ?? 0) + 1,
                ])->save();
                $sent++;
            } catch (\Throwable $exception) {
                report($exception);
                $invite->forceFill([
                    'email_last_attempt_at' => now(),
                    'email_attempts' => ($invite->email_attempts ?? 0) + 1,
                ])->save();
                $skipped++;
            }
        }

        $this->info("Sent {$sent} invitation(s); {$skipped} skipped.");

        return Command::SUCCESS;
    }
}
