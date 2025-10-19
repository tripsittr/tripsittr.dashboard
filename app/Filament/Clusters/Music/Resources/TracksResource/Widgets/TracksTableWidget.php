<?php

namespace App\Filament\Clusters\Music\Resources\TracksResource\Widgets;

use App\Filament\Clusters\Music\Resources\TracksResource;
use App\Models\Song;
use Filament\Facades\Filament;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Table;
use Filament\Tables\View\TablesRenderHook;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TracksTableWidget extends BaseWidget
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
                Song::query()
                    ->where('album_id', $this->albumId)
                    ->orderBy('track_number')
                    ->orderBy('title')
            )
            ->columns([
                // Row index like streaming platforms
                Tables\Columns\TextColumn::make('index')
                    ->label('#')
                // ...existing columns...
            ]);
    }
}
