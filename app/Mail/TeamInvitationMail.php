<?php

namespace App\Mail;

use App\Models\Invitation;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\URL;

class TeamInvitationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(public Invitation $invitation)
    {
    }

    public function build(): self
    {
        $team = $this->invitation->team;        
        $acceptUrl = route('invitations.show', $this->invitation->token);
        // Direct registration URL with pre-filled email & invitation token
        $registerUrl = route('register', [
            'email' => $this->invitation->email,
            'invitation' => $this->invitation->token,
        ]);
        $expires = $this->invitation->expires_at;
        $inviter = $this->invitation->inviter;

        return $this->subject('Invitation to join '.$team->name)
            ->markdown('mail.invitations.team', [
                'invitation' => $this->invitation,
                'team' => $team,
                'acceptUrl' => $acceptUrl,
                'registerUrl' => $registerUrl,
                'expires' => $expires,
                'inviter' => $inviter,
            ]);
    }
}
