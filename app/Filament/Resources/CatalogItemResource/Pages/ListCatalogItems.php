<?php

namespace App\Filament\Resources\CatalogItemResource\Pages;

use App\Filament\Resources\CatalogItemResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Auth;

class ListCatalogItems extends ListRecords
{
    protected static string $resource = CatalogItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->visible(function(){
                    $user = Auth::user();
                    if(!$user) return false;
                    if($tenant = Filament::getTenant()) { app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($tenant->id); }
                    return $user->can('create catalog items') || $user->hasRole('Admin') || $user->hasRole('Manager');
                })
        ];
    }
}
