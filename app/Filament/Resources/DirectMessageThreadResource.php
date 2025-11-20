<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DirectMessageThreadResource\Pages;
use App\Models\DirectMessageThread;
use Filament\Resources\Resource;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;

class DirectMessageThreadResource extends Resource
{
    protected static ?string $model = DirectMessageThread::class;
    protected static ?string $label = 'Messages';
    protected static ?string $navigationLabel = null;
    public static function shouldRegisterNavigation(): bool { return false; }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('subject')
                    ->label('Subject')
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('subject')->label('Subject'),
                Tables\Columns\TextColumn::make('created_at')->dateTime(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDirectMessageThreads::route('/'),
            'view' => Pages\ViewDirectMessageThread::route('/{record}'),
        ];
    }
}
