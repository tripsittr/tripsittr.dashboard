<?php

namespace App\Filament\Index\Mail\Events\Users;

use Filament\Facades\Filament;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class ModelCreated extends Mailable
{
    use Queueable, SerializesModels;

    public $model;

    public $action_type;

    public $userId;

    public $teamId;

    public function __construct($model, $actionType, $userId, $teamId)
    {
        $this->model = $model;
        $this->action_type = $actionType;
        $this->userId = $userId;
        $this->teamId = $teamId;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: 'support@tripsittr.com', // Use the default "From" address
            subject: 'User Has Been Created',
        );
    }

    public function content(): Content
    {

        return new Content(
            view: 'emails.events.users.user_created',
            with: [
                'model' => $this->model,
                'user' => Auth::user(), // Get the authenticated user
                'tenant' => Filament::getTenant(), // Get the tenant information
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
