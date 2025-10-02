<?php

namespace App\Mail\Events\Albums;

use Filament\Facades\Filament;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class ModelCreated extends Mailable
{
    use Queueable, SerializesModels;

    public $model;

    public function __construct($model)
    {
        $this->model = $model;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: 'support@tripsittr.com',
            subject: 'Album Has Been Created',
        );
    }

    public function content(): Content
    {

        return new Content(
            view: 'emails.events.albums.album_created',
            with: [
                'model' => $this->model,
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
