<?php

namespace App\Filament\Resources\SongResource\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class SongsTableWidget extends BaseWidget {
    public ?int $albumId = null;

    public function albumId(int $albumId): static {
        $this->albumId = $albumId;
        return $this;
    }

    public function table(Table $table): Table {
        return $table
            ->query(
                \App\Models\Song::query()
                    ->where('album_id', $this->albumId)
            )
            ->columns([
                // ...
            ]);
    }
}
