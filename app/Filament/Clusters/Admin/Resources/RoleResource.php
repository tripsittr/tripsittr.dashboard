<?php

namespace App\Filament\Clusters\Admin\Resources;

use App\Filament\Clusters\Admin;
use App\Filament\Clusters\Admin\Resources\RoleResource\Pages;
use Filament\Facades\Filament;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Unique;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-s-shield-check';

    protected static ?string $navigationGroup = 'Administration';

    protected static ?int $navigationSort = 65;

    protected static ?string $cluster = Admin::class;

    // Spatie Role model has no `team` relationship; avoid Filament tenant scoping
    protected static bool $isScopedToTenant = false;

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }

    public static function canAccess(): bool
    {
        $user = Auth::user();
        if (! $user) {
            return false;
        }

        return ($user->type === 'Admin') || ($user->hasRole('Admin'));
    }

    // Ensure Filament treats Admins as authorized for common abilities even if granular permissions aren't assigned
    public static function canViewAny(): bool
    {
        $user = Auth::user();
        if (! $user) {
            return false;
        }

        return static::canAccess() || $user->can('view roles') || $user->can('manage roles');
    }

    public static function canCreate(): bool
    {
        $user = Auth::user();
        if (! $user) {
            return false;
        }

        return static::canAccess() || $user->can('manage roles');
    }

    public static function canEdit($record): bool
    {
        $user = Auth::user();
        if (! $user) {
            return false;
        }

        return static::canAccess() || $user->can('manage roles');
    }

    public static function canDelete($record): bool
    {
        $user = Auth::user();
        if (! $user) {
            return false;
        }

        return static::canAccess() || $user->can('manage roles');
    }

    public static function getEloquentQuery(): Builder
    {
        $tenant = Filament::getTenant();
        $query = parent::getEloquentQuery();
        if ($tenant) {
            $query->where('team_id', $tenant->id);
        } else {
            $query->whereNull('team_id');
        }

        return $query->where('guard_name', 'web');
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->label('Role Name')
                ->required()
                ->maxLength(120)
                ->unique(
                    table: 'roles',
                    column: 'name',
                    ignoreRecord: true,
                    modifyRuleUsing: function (Unique $rule) {
                        $tenant = Filament::getTenant();
                        if ($tenant) {
                            $rule->where('team_id', $tenant->id);
                        } else {
                            $rule->whereNull('team_id');
                        }
                    }
                ),

            Hidden::make('guard_name')->default('web'),

            ...static::buildPermissionSections(),
        ]);
    }

    /**
     * Build grouped permission sections with Select All / Clear buttons.
     */
    protected static function buildPermissionSections(): array
    {
        $groups = static::getPermissionGroups();

        $sections = [];
        foreach ($groups as $groupKey => $group) {
            $fieldName = $group['field'];
            $label = $group['label'];
            $options = $group['options'];

            $sections[] = Section::make($label)
                ->collapsible()
                ->compact()
                ->headerActions([
                    FormAction::make('select_all_'.$groupKey)
                        ->label('Select all')
                        ->action(function (Set $set) use ($fieldName, $options) {
                            $set($fieldName, array_keys($options));
                        })
                        ->color('primary')
                        ->icon('heroicon-m-check-circle'),
                    FormAction::make('clear_'.$groupKey)
                        ->label('Clear')
                        ->action(function (Set $set) use ($fieldName) {
                            $set($fieldName, []);
                        })
                        ->color('gray')
                        ->icon('heroicon-m-x-mark'),
                ])
                ->schema([
                    CheckboxList::make($fieldName)
                        ->label('')
                        ->options($options)
                        ->columns(3)
                        ->bulkToggleable()
                        ->afterStateHydrated(function (CheckboxList $component, $state, $record) use ($options) {
                            if (! $record) {
                                return;
                            }
                            $current = $record->permissions()->pluck('id')->all();
                            $component->state(array_values(array_intersect($current, array_keys($options))));
                        }),
                ]);
        }

        return $sections;
    }

    /**
     * Compute permission groups from Permission model into labeled option arrays.
     * Returns an array like:
     * [
     *   'users' => ['label' => 'Users & Team', 'field' => 'perm_users', 'options' => [id => name, ...]],
     *   ...
     * ]
     */
    protected static function getPermissionGroups(): array
    {
        $all = Permission::query()
            ->where('guard_name', 'web')
            ->orderBy('name')
            ->get(['id', 'name']);

        $buckets = [
            'users' => [
                'label' => 'Users & Team',
                'field' => 'perm_users',
                'match' => ['user', 'users', 'team', 'roles', 'permissions'],
                'options' => [],
            ],
            'catalog' => [
                'label' => 'Catalog',
                'field' => 'perm_catalog',
                'match' => ['song', 'songs', 'album', 'albums'],
                'options' => [],
            ],
            'social' => [
                'label' => 'Social / Marketing',
                'field' => 'perm_social',
                'match' => ['social', 'marketing'],
                'options' => [],
            ],
            'events' => [
                'label' => 'Events / Bookings',
                'field' => 'perm_events',
                'match' => ['event', 'events', 'booking', 'bookings'],
                'options' => [],
            ],
            'sales' => [
                'label' => 'Sales / Merch',
                'field' => 'perm_sales',
                'match' => ['merch', 'sale', 'sales'],
                'options' => [],
            ],
            'other' => [
                'label' => 'Other',
                'field' => 'perm_other',
                'match' => [],
                'options' => [],
            ],
        ];

        foreach ($all as $perm) {
            $name = strtolower($perm->name);
            $matched = false;
            foreach ($buckets as $key => &$bucket) {
                if (! empty($bucket['match'])) {
                    foreach ($bucket['match'] as $needle) {
                        if (str_contains($name, $needle)) {
                            $bucket['options'][$perm->id] = $perm->name;
                            $matched = true;
                            break 2;
                        }
                    }
                }
            }
            unset($bucket);
            if (! $matched) {
                $buckets['other']['options'][$perm->id] = $perm->name;
            }
        }

        // remove empty groups
        return array_filter($buckets, fn ($b) => ! empty($b['options']));
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->label('Role')->searchable(),
                TextColumn::make('permissions_count')
                    ->label('Permissions')
                    ->counts('permissions'),
                TextColumn::make('created_at')->since()->label('Created'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn ($record) => (
                        Auth::user()?->type === 'Admin'
                        || Auth::user()?->hasRole('Admin')
                        || Auth::user()?->can('manage roles')
                    )),
                Tables\Actions\DeleteAction::make()
                    ->visible(fn ($record) => (
                        Auth::user()?->type === 'Admin'
                        || Auth::user()?->hasRole('Admin')
                        || Auth::user()?->can('manage roles')
                    )),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}
