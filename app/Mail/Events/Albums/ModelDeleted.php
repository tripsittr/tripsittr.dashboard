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

class ModelDeleted extends Mailable
{
    use Queueable, SerializesModels;

    public $model; // Add a public property to hold the model data

    public function __construct($model)
    {
        $this->model = $model; // Pass the model to the mailable
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: 'support@tripsittr.com',
            subject: 'Album Has Been Deleted',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.events.albums.album_deleted',
            with: [
                'model' => $this->model,
                'user' => Auth::user(),
                'tenant' => Filament::getTenant(),
            ],
        );
    }

    /**
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
