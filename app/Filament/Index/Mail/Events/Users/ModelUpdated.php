<?php
namespace App\Filament\Index\Mail\Events\Users;

use Filament\Facades\Filament;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class ModelUpdated extends Mailable
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
            subject: 'User Has Been Updated',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.events.users.user_updated',
            with: [
                'model' => $this->model,
                'user' => Auth::user(),
                'tenant' => Filament::getTenant(),
            ],
        );
    }

    /**null
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
