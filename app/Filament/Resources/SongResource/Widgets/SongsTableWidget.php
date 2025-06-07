<?php

namespace App\Filament\Resources\SongResource\Widgets;

use Filament\Facades\Filament;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Table;
use Filament\Tables\View\TablesRenderHook;
use Filament\Widgets\TableWidget as BaseWidget;

class SongsTableWidget extends BaseWidget
{
    public ?int $albumId = null;

    public function albumId(int $albumId): static
    {
        $this->albumId = $albumId;
        return $this;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                \App\Models\Song::query()
                    ->where('album_id', $this->albumId)
            )
            ->columns([
                Tables\Columns\ImageColumn::make('artwork')
                    ->label('Artwork')
                    ->circular()
                    ->size(100),
                Tables\Columns\TextColumn::make('title')
                    ->label('Name')
                    ->weight(FontWeight::Bold)
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('genre')
                    ->label('Genre')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('isrc')
                    ->label('ISRC')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('visibility')
                    ->label('Visibility')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->sortable()
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Edit')
                    ->icon('heroicon-o-pencil')
                    ->url(fn($record) => route('filament.admin.resources.songs.edit', ['record' => $record, 'tenant' => Filament::getTenant()->id])),
                Tables\Actions\DeleteAction::make()
                    ->label('Delete')
                    ->icon('heroicon-o-trash')
                    ->action(fn($record) => $record->delete()),
            ]);
    }
}
