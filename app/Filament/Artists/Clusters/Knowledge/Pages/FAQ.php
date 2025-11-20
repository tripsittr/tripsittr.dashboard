<?php

namespace App\Filament\Artists\Clusters\Knowledge\Pages;

use App\Filament\Artists\Clusters\Knowledge\Knowledge;
use Filament\Pages\Page;
use Filament\Resources\Pages\Concerns\InteractsWithRecord;

class FAQ extends Page
{
    protected static ?string $navigationIcon = 'heroicon-s-question-mark-circle';

    protected static string $view = 'filament.clusters.knowledge.pages.f-a-q';

    protected static ?int $navigationSort = 4;

    protected static null|string $title = 'FAQ';

    protected static ?string $cluster = Knowledge::class;
}
