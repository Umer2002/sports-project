<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InviteEmail extends Mailable
{
    use Queueable, SerializesModels;

    public string $inviteLink;
    public $user;
    public array $meta;

    /**
     * Create a new message instance.
     */
    public function __construct(string $inviteLink, $user, array $meta = [])
    {
        $this->inviteLink = $inviteLink;
        $this->user = $user;
        $this->meta = $meta;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->meta['subject'] ?? 'You are invited to Play2Earn Sports!',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.invite',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    public function build()
    {
        $subject = $this->meta['subject'] ?? 'You are invited to Play2Earn Sports!';

        return $this->subject($subject)
            ->view('emails.invite')
            ->with([
                'inviteLink' => $this->inviteLink,
                'user' => $this->user,
                'meta' => $this->meta,
            ]);
    }
}
