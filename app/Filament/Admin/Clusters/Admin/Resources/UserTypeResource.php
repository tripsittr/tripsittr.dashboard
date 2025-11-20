<?php
namespace App\Filament\Admin\Clusters\Admin\Resources;

use App\Filament\Admin\Clusters\Admin\Admin;
use App\Filament\Admin\Clusters\Admin\Resources\UserTypeResource\Pages;
use App\Models\UserType;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class UserTypeResource extends Resource
{
    protected static ?string $model = UserType::class;

    protected static ?string $navigationIcon = 'heroicon-s-identification';
    protected static ?string $navigationGroup = 'Administration';
    protected static ?int $navigationSort = 60;

    protected static ?string $cluster = Admin::class;

    // User types are global, not tenant-owned
    protected static bool $isScopedToTenant = false;

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }

    public static function canAccess(): bool
    {
        $user = Auth::user();
        if (! $user) return false;
        return ($user->type === 'Admin') || ($user->hasRole('Admin'));
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->required()
                ->unique(ignoreRecord: true)
                ->maxLength(120),
            Textarea::make('description')
                ->rows(3)
                ->maxLength(500)
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')->sortable(),
                TextColumn::make('name')->searchable()->label('Type'),
                TextColumn::make('description')->limit(60),
                TextColumn::make('created_at')->since()->label('Created'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUserTypes::route('/'),
            'create' => Pages\CreateUserType::route('/create'),
            'edit' => Pages\EditUserType::route('/{record}/edit'),
        ];
    }
}
