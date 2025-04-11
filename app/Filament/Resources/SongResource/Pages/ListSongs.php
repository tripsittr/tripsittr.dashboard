<?php

namespace App\Filament\Resources\SongResource\Pages;

use App\Filament\Resources\SongResource;
use Filament\Resources\Pages\Page;
use Filament\Actions;
use App\Models\Song;
use Filament\Resources\Pages\ListRecords;

class ListSongs extends ListRecords {
    protected static string $resource = SongResource::class;
    // protected static string $view = 'filament.pages.list-songs';

    // protected function getHeaderActions(): array {
    //     return [
    //         Actions\CreateAction::make()
    //             ->label('New Song')
    //             ->icon('heroicon-s-plus')
    //             ->url(fn() => SongResource::getUrl('create')),
    //     ];
    // }

    // public function getViewData(): array {
    //     return [
    //         'songs' => Song::latest()->get(),
    //     ];
    // }
}
