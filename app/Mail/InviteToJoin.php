<?php

namespace App\Mail;

use App\Models\Invite;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InviteToJoin extends Mailable
{
    use Queueable, SerializesModels;

    public $invite;

    /**
     * Create a new message instance.
     */
    public function __construct(Invite $invite)
    {
        $this->invite = $invite;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $user = \App\Models\User::find($this->invite->sender_id);
        $inviteLink = url('/register?email=' . urlencode($this->invite->receiver_email));
        
        return $this->subject('You are invited to join our platform')
                    ->view('emails.invite')
                    ->with([
                        'user' => $user,
                        'inviteLink' => $inviteLink
                    ]);
    }
}
