<?php

namespace App\Filament\Clusters\Knowledge\Resources\KnowledgeResource\Pages;

use App\Filament\Clusters\Knowledge\Resources\KnowledgeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKnowledge extends EditRecord
{
    protected static string $resource = KnowledgeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
