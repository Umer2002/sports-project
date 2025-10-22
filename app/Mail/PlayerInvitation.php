<?php

namespace App\Mail;

use App\Models\Club;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;

class PlayerInvitation extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $club;

    public $personalMessage;

    public $recipientName;

    public $inviteLink;

    public function __construct(Club $club, ?string $personalMessage = null, ?string $recipientName = null, ?string $inviteLink = null)
    {
        $this->club = $club;
        $this->personalMessage = $personalMessage;
        $this->recipientName = $recipientName;
        $this->inviteLink = $inviteLink;
    }

    public function build()
    {
        return $this->subject('Invitation to join ' . $this->club->name)
                    ->markdown('emails.invitation', [
                        'club' => $this->club,
                        'personalMessage' => $this->personalMessage,
                        'recipientName' => $this->recipientName,
                        'inviteLink' => $this->inviteLink,
                    ]);
    }
}
