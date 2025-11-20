<?php

namespace App\Filament\Artists\Clusters\Knowledge\Resources\VenueResource\Pages;

use App\Filament\Artists\Clusters\Knowledge\Resources\VenueResource;
use App\Filament\Imports\VenueImporter;
use App\Models\Venue;
use Filament\Actions;
use Filament\Facades\Filament;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListVenues extends ListRecords
{
    protected static string $resource = VenueResource::class;

    // public $tenant;
    // public $venues;

    // public function mount(): void {
    //     parent::mount();

    //     $this->tenant = Auth::user()->teams()->first();
    //     $this->venues = Venue::all();
    // }

    // protected static string $view = 'filament.pages.list-venues';

    protected function getHeaderActions(): array
    {
        return [
            Actions\ImportAction::make()
                ->importer(VenueImporter::class)
                ->visible(function () {
                    $user = Auth::user();
                    if (! $user) {
                        return false;
                    }
                    if ($tenant = Filament::getTenant()) {
                        app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($tenant->id);
                    }

                    return $user->can('create venues') || $user->hasRole('Admin') || $user->hasRole('Manager');
                }),
            Actions\CreateAction::make()
                ->visible(function () {
                    $user = Auth::user();
                    if (! $user) {
                        return false;
                    }
                    if ($tenant = Filament::getTenant()) {
                        app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($tenant->id);
                    }

                    return $user->can('create venues') || $user->hasRole('Admin') || $user->hasRole('Manager');
                }),
        ];
    }
}
