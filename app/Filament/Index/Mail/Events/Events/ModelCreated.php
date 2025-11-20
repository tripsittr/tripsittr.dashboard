<?php
namespace App\Filament\Index\Mail\Events\Events;

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

    public $model; // Add a public property to hold the model data

    public function __construct($model)
    {
        $this->model = $model; // Pass the model to the mailable
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: 'support@tripsittr.com', // Use the default "From" address
            subject: 'Event Has Been Created',
        );
    }

    public function content(): Content
    {

        return new Content(
            view: 'emails.events.events.event_created',
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
