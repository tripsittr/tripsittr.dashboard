<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\MembersTable;
use Filament\Pages\Page;

class ListTeamMembers extends Page
{
    protected static ?string $navigationIcon = 'heroicon-s-users';
    protected static string $view = 'filament.pages.partners';
    protected static ?string $title = 'Manage Team Members';
    protected static ?string $slug = 'team-members';

    protected static bool $shouldRegisterNavigation = false;

    protected function getHeaderWidgets(): array
    {
        return [
            MembersTable::class,
        ];
    }
}
