<?php
namespace App\Filament\Admin\Clusters\Admin\Pages;

use App\Filament\Admin\Clusters\Admin\Admin;
use Filament\Pages\Page;

class EmailTests extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.clusters.admin.pages.email-tests';

    protected static ?string $cluster = Admin::class;
}
