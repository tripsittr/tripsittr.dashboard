<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Auth;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('New Order')
                ->visible(function(){
                    $user = Auth::user();
                    if(!$user) return false;
                    if($tenant = Filament::getTenant()) { app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($tenant->id); }
                    return $user->can('create orders') || $user->hasRole('Admin') || $user->hasRole('Manager');
                })
        ];
    }
}
