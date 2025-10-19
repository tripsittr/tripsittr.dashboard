<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActionResource\Pages;
use App\Models\Action;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class ActionResource extends Resource
{
    protected static ?string $model = Action::class;
    protected static ?string $cluster = \App\Filament\Clusters\Admin::class; // system-wide actions belong in Admin cluster
    protected static ?int $navigationSort = 50;
    protected static ?string $navigationIcon = 'heroicon-s-bolt';
    // This resource is system-wide and not tenant-owned, so do not scope to tenant
    protected static bool $isScopedToTenant = false;

    public static function shouldRegisterNavigation(): bool
    {
        return Auth::check() && Auth::user()->type === 'Admin';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('action_title')->required()->maxLength(120),
                TextInput::make('action_type')->required()->unique(ignoreRecord: true)->maxLength(120),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('id')->sortable(),
            TextColumn::make('action_title')->searchable()->label('Title'),
            TextColumn::make('action_type')->badge()->searchable()->label('Type'),
            TextColumn::make('created_at')->since()->label('Created'),
        ])->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])->bulkActions([
            Tables\Actions\DeleteBulkAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListActions::route('/'),
            'create' => Pages\CreateAction::route('/create'),
            'edit' => Pages\EditAction::route('/{record}/edit'),
        ];
    }
}
