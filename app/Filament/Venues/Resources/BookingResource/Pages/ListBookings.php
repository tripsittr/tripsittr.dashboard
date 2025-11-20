<?php

namespace App\Filament\Venues\Resources\BookingResource\Pages;

use App\Filament\Venues\Resources\BookingResource;
use Filament\Resources\Pages\ListRecords;

class ListBookings extends ListRecords
{
    protected static string $resource = BookingResource::class;
}
