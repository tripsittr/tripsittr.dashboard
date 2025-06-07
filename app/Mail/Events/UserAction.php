<?php

namespace App\Mail\Events;

use Filament\Facades\Filament;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class UserAction extends Mailable
{
    use Queueable, SerializesModels;

    public $model;
    public $action;
    public $userId;
    public $teamId;

    public function __construct($model, $action, $userId, $teamId)
    {
        $this->model = $model;
        $this->action = $action;
        $this->userId = $userId;
        $this->teamId = $teamId;
    }

    public function envelope(): Envelope
    {
        $userName = \App\Models\User::find($this->userId)?->name ?? 'Unknown User';

        return new Envelope(
            from: 'support@tripsittr.com', // Use the default "From" address
            subject: "Activity Log: {$userName} performed {$this->action}",
        );
    }

    public function content(): Content
    {

        return new Content(
            view: 'emails.events.user_action_log',
            with: [
                'model' => $this->model,
                'action' => $this->action,
                'userId' => $this->userId,
                'teamId' => $this->teamId,
                'user' => Auth::user(),
                'tenant' => Filament::getTenant(),
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
