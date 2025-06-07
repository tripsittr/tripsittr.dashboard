<?php

namespace App\Filament\Clusters\Partners;

use App\Filament\Clusters\Partners;

use Filament\Pages\Page;

class FortVineBand extends Page
{
    protected static ?string $title = 'Fort Vine';

    protected static string $view = 'filament.pages.fort-vine';

    protected static ?string $cluster = Partners::class;
}
