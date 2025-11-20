<?php

namespace App\Filament\Venues\Resources\BookingResource\RelationManagers;

use App\Models\DirectMessageThread;
use App\Models\DirectMessage;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class MessagesRelationManager extends RelationManager
{
    protected static string $relationship = 'messages'; // Not used, but required by Filament

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Textarea::make('body')
                ->label('Message')
                ->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('sender_id')->label('Sender'),
            Tables\Columns\TextColumn::make('body')->label('Message'),
            Tables\Columns\TextColumn::make('created_at')->dateTime(),
        ])->headerActions([
            Tables\Actions\CreateAction::make()
                ->mutateFormDataUsing(function (array $data) {
                    $data['sender_id'] = Auth::id();
                    $data['sender_type'] = 'App\\Models\\User';
                    return $data;
                }),
        ]);
    }

    /**
     * Override Filament's relationship query to show messages for the booking's thread.
     */
    public function getRelationshipQuery()
    {
        $booking = $this->getOwnerRecord();
        $thread = DirectMessageThread::where('booking_request_id', $booking->request_id)->first();
        if ($thread) {
            return $thread->messages();
        }
        // Return empty query if no thread exists
        return (new DirectMessage())->newQuery()->whereRaw('1=0');
    }
}
