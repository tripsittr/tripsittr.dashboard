<?php
namespace App\Filament\Admin\Clusters\Admin\Resources;

use App\Filament\Admin\Clusters\Admin\Resources\TeamResource\Pages;
use App\Models\Team;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Admin\Clusters\Admin\Admin;

class TeamResource extends Resource
{
    protected static ?string $model = Team::class;

    protected static ?string $tenantOwnershipRelationshipName = 'users';

    protected static ?string $navigationIcon = 'heroicon-s-user-group';

    protected static ?int $navigationSort = 15;

    protected static ?string $cluster = Admin::class;

    protected static ?string $navigationLabel = 'Teams';

    public static function shouldRegisterNavigation(): bool
    {
        return true; // already constrained to Admin cluster visibility
    }

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->required(),
                Forms\Components\Select::make('type')->required()
                    ->options([
                        'Solo Artist' => 'Solo Artist',
                        'Band' => 'Band',
                        'Management Agency' => 'Management Agency',
                        'Record Label' => 'Record Label',
                    ]),
                Forms\Components\FileUpload::make('team_avatar')->image()->avatar()->nullable(),
                Forms\Components\TextInput::make('stripe_id')->nullable(),
                Forms\Components\TextInput::make('pm_type')->nullable(),
                Forms\Components\TextInput::make('pm_last_four')->nullable(),
                Forms\Components\TextInput::make('tax_rate')->nullable(),
                Forms\Components\TextInput::make('plan_slug')->nullable(),
                Forms\Components\DatePicker::make('plan_started_at')->nullable(),
                Forms\Components\DatePicker::make('plan_renews_at')->nullable(),
                Forms\Components\DatePicker::make('trial_ends_at')->nullable(),
                Forms\Components\DatePicker::make('created_at')->disabled(),
                Forms\Components\DatePicker::make('updated_at')->disabled(),
                Forms\Components\DatePicker::make('deleted_at')->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->withoutGlobalScopes())
            ->columns([
                TextColumn::make('id')->sortable()->label('ID'),
                Tables\Columns\ImageColumn::make('team_avatar')->label('Avatar')->circular()->size(32),
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('type')->badge()->sortable(),
                TextColumn::make('stripe_id')->toggleable(),
                TextColumn::make('pm_type')->toggleable(),
                TextColumn::make('pm_last_four')->toggleable(),
                TextColumn::make('tax_rate')->toggleable(),
                TextColumn::make('plan_slug')->toggleable(),
                TextColumn::make('plan_started_at')->date()->toggleable(),
                TextColumn::make('plan_renews_at')->date()->toggleable(),
                TextColumn::make('trial_ends_at')->date()->toggleable(),
                TextColumn::make('created_at')->since()->sortable(),
                TextColumn::make('updated_at')->since()->sortable(),
                TextColumn::make('deleted_at')->date()->toggleable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTeams::route('/'),
            'create' => Pages\CreateTeam::route('/create'),
            'edit' => Pages\EditTeam::route('/{record}/edit'),
            // 'manage-users' is a custom page, not a resource page route
        ];
    }

    // TEMP: allow everything to debug 403
    public static function canViewAny(): bool
    {
        return true;
    }

    public static function canEdit($record): bool
    {
        return true;
    }

    public static function canCreate(): bool
    {
        return true;
    }

    public static function canDelete($record): bool
    {
        return true;
    }

    public static function canDeleteAny(): bool
    {
        return true;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes();
    }
}
