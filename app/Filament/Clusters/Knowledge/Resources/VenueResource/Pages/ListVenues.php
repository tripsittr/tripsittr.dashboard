<?php

namespace App\Filament\Clusters\Knowledge\Resources\VenueResource\Pages;

use App\Filament\Imports\VenueImporter;
use App\Filament\Clusters\Knowledge\Resources\VenueResource;
use App\Models\Venue;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListVenues extends ListRecords {
    protected static string $resource = VenueResource::class;

    // public $tenant;
    // public $venues;

    // public function mount(): void {
    //     parent::mount();

    //     $this->tenant = Auth::user()->teams()->first();
    //     $this->venues = Venue::all();
    // }

    // protected static string $view = 'filament.pages.list-venues';

    protected function getHeaderActions(): array {
        return [
            Actions\ImportAction::make()
                ->importer(VenueImporter::class),
            Actions\CreateAction::make(),
        ];
    }
}
