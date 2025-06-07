<?php

namespace App\Filament\Clusters\Partners;

use App\Filament\Clusters\Partners;

use Filament\Pages\Page;

class AurumThreads extends Page
{
    protected static ?string $title = 'Aurum Threads';

    protected static string $view = 'filament.pages.aurum-threads';

    protected static ?string $cluster = Partners::class;
}
