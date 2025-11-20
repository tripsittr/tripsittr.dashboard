<?php
namespace App\Filament\Artists\Clusters\Commerce\Resources\CustomerResource\Pages;

use App\Filament\Artists\Clusters\Commerce\Resources\CustomerResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Auth;

class ListCustomers extends ListRecords
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->visible(function(){
                    $user = Auth::user();
                    if(!$user) return false;
                    if($tenant = Filament::getTenant()) { app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($tenant->id); }
                    return $user->can('create customers') || $user->hasRole('Admin') || $user->hasRole('Manager');
                })
        ];
    }
}
