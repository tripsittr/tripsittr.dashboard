<?php

namespace App\Filament\Clusters\Knowledge\Resources\KnowledgeResource\Pages;

use App\Filament\Clusters\Knowledge\Resources\KnowledgeResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateKnowledge extends CreateRecord
{
    protected static string $resource = KnowledgeResource::class;
}
