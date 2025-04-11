<?php

namespace App\Filament\Imports;

use App\Models\Venue;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class VenueImporter extends Importer {
    protected static ?string $model = Venue::class;

    public static function getColumns(): array {
        return [
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['string', 'max:255']),
            ImportColumn::make('address')
                ->requiredMapping()
                ->rules(['string']),
            ImportColumn::make('phone')
                ->rules(['nullable', 'string', 'max:255']),
            ImportColumn::make('email')
                ->rules(['nullable', 'email', 'max:255']),
            ImportColumn::make('lat')
                ->numeric()
                ->rules(['nullable', 'numeric', 'between:-90,90']),
            ImportColumn::make('lng')
                ->numeric()
                ->rules(['nullable', 'numeric', 'between:-180,180']),
            ImportColumn::make('url')
                ->rules(['nullable', 'url', 'max:255']),
            ImportColumn::make('country')
                ->requiredMapping()
                ->rules(['string', 'max:255']),
            ImportColumn::make('state')
                ->requiredMapping()
                ->rules(['string', 'max:255']),
            ImportColumn::make('city')
                ->requiredMapping()
                ->rules(['string', 'max:255']),
            ImportColumn::make('star_count')
                ->numeric()
                ->rules(['nullable', 'integer', 'min:0']),
            ImportColumn::make('rating_count')
                ->numeric()
                ->rules(['nullable', 'integer', 'min:0']),
            ImportColumn::make('zip')
                ->rules(['nullable', 'string', 'max:255']),
            ImportColumn::make('primary_category_name')
                ->requiredMapping()
                ->rules(['string', 'max:255']),
            ImportColumn::make('category_name')
                ->rules(['nullable', 'string', 'max:255']),
            ImportColumn::make('capacity')
                ->numeric()
                ->rules(['nullable', 'integer', 'min:0']),
            ImportColumn::make('indoor_outdoor')
                ->rules(['nullable', 'string', 'in:indoor,outdoor,both']),
            ImportColumn::make('stage_size')
                ->rules(['nullable', 'string', 'max:255']),
            ImportColumn::make('seating_type')
                ->rules(['nullable', 'string', 'max:255']),
            ImportColumn::make('parking_info')
                ->rules(['nullable', 'string']),
            ImportColumn::make('age_restriction')
                ->rules(['nullable', 'string', 'max:255']),
            ImportColumn::make('alcohol_policy')
                ->rules(['nullable', 'string', 'max:255']),
            ImportColumn::make('booking_contact_name')
                ->rules(['nullable', 'string', 'max:255']),
            ImportColumn::make('booking_email')
                ->rules(['nullable', 'email', 'max:255']),
            ImportColumn::make('booking_phone')
                ->rules(['nullable', 'string', 'max:255']),
            ImportColumn::make('booking_website')
                ->rules(['nullable', 'url', 'max:255']),
            ImportColumn::make('rental_price_range')
                ->rules(['nullable', 'string', 'max:255']),
            ImportColumn::make('sound_equipment_provided')
                ->boolean()
                ->rules(['boolean']),
            ImportColumn::make('lighting_equipment_provided')
                ->boolean()
                ->rules(['boolean']),
            ImportColumn::make('backline_available')
                ->boolean()
                ->rules(['boolean']),
            ImportColumn::make('green_room')
                ->boolean()
                ->rules(['boolean']),
            ImportColumn::make('wifi_available')
                ->boolean()
                ->rules(['boolean']),
            ImportColumn::make('wheelchair_accessible')
                ->boolean()
                ->rules(['boolean']),
            ImportColumn::make('food_beverage_available')
                ->boolean()
                ->rules(['boolean']),
            ImportColumn::make('nearby_hotels')
                ->rules(['nullable', 'string']),
            ImportColumn::make('public_transit_access')
                ->boolean()
                ->rules(['boolean']),
            ImportColumn::make('notes')
                ->rules(['nullable', 'string']),
            ImportColumn::make('facebook_profile')
                ->rules(['nullable', 'url', 'max:255']),
            ImportColumn::make('instagram_handle')
                ->rules(['nullable', 'string', 'max:255']),
            ImportColumn::make('linkedin')
                ->rules(['nullable', 'url', 'max:255']),
            ImportColumn::make('twitter')
                ->rules(['nullable', 'string', 'max:255']),
            ImportColumn::make('whatsapp')
                ->rules(['nullable', 'string', 'max:255']),
            ImportColumn::make('youtube')
                ->rules(['nullable', 'url', 'max:255']),
            ImportColumn::make('tiktok')
                ->rules(['nullable', 'string', 'max:255']),
        ];
    }

    public function resolveRecord(): ?Venue {
        // return Venue::firstOrNew([
        //     // Update existing records, matching them by `$this->data['column_name']`
        //     'email' => $this->data['email'],
        // ]);

        return new Venue();
    }

    public static function getCompletedNotificationBody(Import $import): string {
        $body = 'Your venue import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
}
