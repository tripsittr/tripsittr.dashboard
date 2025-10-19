<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityLogResource\Pages;
use App\Models\ActivityLog;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms; // future enhancement
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ActivityLogResource extends Resource
{
    protected static ?string $model = ActivityLog::class;
    protected static ?string $navigationIcon = 'heroicon-s-clipboard-document-list';
    protected static ?string $cluster = \App\Filament\Clusters\Settings\Settings::class; // moved to Settings cluster
    protected static ?int $navigationSort = 50;
    protected static ?string $navigationLabel = 'Activity Logs';
    protected static ?string $tenantOwnershipRelationshipName = 'team';

    public static function canViewAny(): bool
    {
        $user = Auth::user();
        if (! $user) return false;
        if($tenant = \Filament\Facades\Filament::getTenant()) {
            app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($tenant->id);
        }
        return $user->can('view activity logs') || $user->hasRole('Admin');
    }

    public static function form(\Filament\Forms\Form $form): \Filament\Forms\Form
    {
        return $form->schema([]); // read-only
    }

    public static function table(Table $table): Table
    {
        $actionOptions = ActivityLog::query()->select('action')->distinct()->pluck('action','action')->toArray();
        $entityOptions = ActivityLog::query()->select('entity_type')->distinct()->pluck('entity_type','entity_type')->toArray();

        return $table
            ->heading('Recent Activity')
            ->defaultSort('id','desc')
            ->modifyQueryUsing(function (Builder $query) {
                $tenant = \Filament\Facades\Filament::getTenant();
                if ($tenant) {
                    $query->where('team_id', $tenant->id);
                } else {
                    // If no tenant selected, show nothing to avoid cross-tenant leakage
                    $query->whereRaw('1=0');
                }
            })
            ->columns([
                TextColumn::make('id')->sortable(),
                BadgeColumn::make('action')->colors([
                    'primary' => fn($state) => str_contains($state,'order'),
                    'warning' => fn($state) => str_contains($state,'reserve'),
                    'danger' => fn($state) => str_contains($state,'decrement'),
                    'success' => fn($state) => str_contains($state,'updated'),
                ])->label('Action'),
                TextColumn::make('entity_type')->label('Entity')->sortable()->searchable(),
                TextColumn::make('entity_id')->label('Entity ID')->sortable(),
                TextColumn::make('user_id')->label('User')->sortable(),
                TextColumn::make('changes')->label('Changes')
                    ->formatStateUsing(fn($state) => is_array($state) ? json_encode($state) : $state)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')->since()->label('When')->sortable(),
            ])
            ->filters([
                SelectFilter::make('action')->options($actionOptions)->multiple(),
                SelectFilter::make('entity_type')->options($entityOptions)->multiple(),
                Filter::make('creates')
                    ->label('Creates')
                    ->query(fn(Builder $query) => $query->where('action', 'like', '%.created')),
                Filter::make('updates')
                    ->label('Updates')
                    ->query(fn(Builder $query) => $query->where('action', 'like', '%.updated')),
                Filter::make('deletes')
                    ->label('Deletes')
                    ->query(fn(Builder $query) => $query->where('action', 'like', '%.deleted')),
            ])
            ->actions([])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActivityLogs::route('/'),
        ];
    }
}
