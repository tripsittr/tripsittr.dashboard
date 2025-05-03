<?php

namespace App\Filament\Resources\KnowledgeResource\Pages;

use App\Filament\Resources\KnowledgeResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\Support\Htmlable;

class ViewKnowledge extends ViewRecord {
    protected static string $resource = KnowledgeResource::class;

    public function getTitle(): string | Htmlable {
        return $this->record->title;
    }

    protected function getHeaderActions(): array {
        return [
            Actions\EditAction::make(),
        ];
    }
}
