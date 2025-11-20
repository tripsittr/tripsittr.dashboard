<?php
namespace App\Filament\Artists\Clusters\Commerce\Resources\CustomerResource\Pages;

use App\Filament\Artists\Clusters\Commerce\Resources\CustomerResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomer extends CreateRecord
{
    protected static string $resource = CustomerResource::class;
}
