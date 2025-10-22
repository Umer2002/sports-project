<?php

namespace App\Mail;

use App\Models\Club;
use App\Models\Player;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ClubPlayerRegistration extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Player $player,
        public Club $club,
        public bool $isNewClub
    ) {
    }

    public function build(): self
    {
        $subject = $this->isNewClub
            ? sprintf('%s listed %s while joining Play2Earn', $this->player->name, $this->club->name)
            : sprintf('%s just joined %s on Play2Earn', $this->player->name, $this->club->name);

        return $this->subject($subject)
            ->view('emails.clubs.player_registered')
            ->with([
                'player' => $this->player,
                'club' => $this->club,
                'isNewClub' => $this->isNewClub,
            ]);
    }
}
