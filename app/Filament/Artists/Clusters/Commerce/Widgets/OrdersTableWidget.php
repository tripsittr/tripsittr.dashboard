<?php

namespace App\Filament\Artists\Clusters\Commerce\Widgets;

use App\Models\Order;
use Filament\Facades\Filament;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class OrdersTableWidget extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    // Do not redeclare the tableRecordsPerPage property here — leave pagination
    // defaults to the parent widget to avoid type/static redeclaration issues
    // caused by mismatched signatures across Filament versions.

    public function table(Table $table): Table
    {
        $tenant = Filament::getTenant();
        $teamId = $tenant?->id;

        $query = Order::query()->orderBy('created_at', 'desc');
        if ($teamId) {
            $query->where('team_id', $teamId);
        }

        return $table
            ->query($query)
            ->columns([
                TextColumn::make('id')->label('ID')->sortable(),
                TextColumn::make('reference')->label('Reference')->wrap()->limit(30),
                TextColumn::make('status')->label('Status')->sortable(),
                TextColumn::make('total')->label('Total')->formatStateUsing(fn ($state) => $state ? '$'.number_format($state, 2) : '—')->sortable(),
                TextColumn::make('created_at')->label('Created')->since()->sortable(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
