<?php

namespace App\Filament\Clusters\Admin\Resources\UserResource\Pages;

use App\Filament\Clusters\Admin\Resources\UserResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Song;
use App\Models\Album;
use App\Models\Band;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use App\Filament\Widgets\UserOverviewWidget;
use Awcodes\Recently\Concerns\HasRecentHistoryRecorder;

class ViewUser extends ViewRecord {
    use HasRecentHistoryRecorder;

    protected static string $resource = UserResource::class;

    public function getRecordTitle(): string {
        return "{$this->record->name}'s Profile";
    }

    public function getHeaderActions(): array {
        return [
            EditAction::make(), // Adds an "Edit" button
            DeleteAction::make(), // Adds a "Delete" button
        ];
    }

    public function getRelations(): array {
        return [
            'songs' => $this->record->songs()->get(),
            'albums' => $this->record->albums()->get(),
            'bands' => $this->record->bands()->get(),
        ];
    }


    public function table(Table $table): Table {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Name'),
                Tables\Columns\TextColumn::make('email')->label('Email'),
                Tables\Columns\TextColumn::make('type')->label('User Type'),
                Tables\Columns\TextColumn::make('bands.name')->label('Band')->sortable(),
            ])
            ->filters([
                //
            ]);
    }
}
