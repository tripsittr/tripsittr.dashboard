<?php

namespace App\Filament\Resources\KnowledgeResource\Pages;

use App\Filament\Resources\KnowledgeResource;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListKnowledge extends ListRecords {
    protected static string $resource = KnowledgeResource::class;

    protected function getHeaderActions(): array {
        return [
            Actions\CreateAction::make()
                ->hidden(fn(): bool => !Auth::user()->type == 'admin'),
        ];
    }
}
