<?php

namespace App\Filament\Resources\DirectMessageThreadResource\Pages;

use App\Filament\Resources\DirectMessageThreadResource;
use App\Models\DirectMessage;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;

class ViewDirectMessageThread extends ViewRecord
{
    protected static string $resource = DirectMessageThreadResource::class;

    public $newMessage = '';

    public $attachment;

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Resources\DirectMessageThreadResource\Widgets\DirectMessageList::make(),
        ];
    }

    public function sendMessage()
    {
        $this->validate([
            'newMessage' => 'required|string',
            'attachment' => 'nullable|file|max:10240', // 10MB max
        ]);

        $message = new DirectMessage;
        $message->fill([
            'thread_id' => $this->record->id,
            'sender_id' => Auth::id(),
            'sender_type' => get_class(Auth::user()),
            'body' => $this->newMessage,
        ]);
        $message->save();

        if ($this->attachment instanceof UploadedFile) {
            $message->uploadAttachment($this->attachment);
        }

        // Notify all other participants in the thread
        $participants = $this->record->participants()->where('participant_id', '!=', Auth::id())->get();
        foreach ($participants as $participant) {
            if ($participant->participant_type === get_class(Auth::user()) && $user = \App\Models\User::find($participant->participant_id)) {
                Notification::make()
                    ->title('New direct message')
                    ->body('You have a new message in "'.($this->record->subject ?? 'Direct Message').'".')
                    ->sendToDatabase($user);
            }
        }

        $this->newMessage = '';
        $this->attachment = null;

        Notification::make()
            ->title('Message sent!')
            ->success()
            ->send();
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Textarea::make('newMessage')
                ->label('Type your message')
                ->rows(3)
                ->required(),
            Forms\Components\FileUpload::make('attachment')
                ->label('Attach a file')
                ->disk('private')
                ->directory('direct_message_attachments')
                ->maxSize(10240)
                ->preserveFilenames(),
        ];
    }
}
