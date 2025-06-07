<?php

namespace App\Filament\Clusters\Admin\Pages;

use App\Filament\Clusters\Admin;
use Filament\Pages\Page;

class EmailTests extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.clusters.admin.pages.email-tests';

    protected static ?string $cluster = Admin::class;
}
