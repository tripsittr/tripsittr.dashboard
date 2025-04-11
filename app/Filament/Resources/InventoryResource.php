<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InventoryResource\Pages;
use App\Filament\Resources\InventoryResource\RelationManagers;
use App\Models\Inventory;
use App\Models\InventoryItem;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
#codebase
class InventoryResource extends Resource {
    protected static ?string $model = InventoryItem::class;

    protected static ?string $navigationIcon = 'heroicon-s-archive-box';
    protected static ?string $navigationLabel = 'Inventory';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form {
        return $form
            ->schema([
                Section::make('Product Details')
                    ->schema([
                        TextInput::make('sku')->required(),
                        TextInput::make('batch_number')->nullable(),
                        TextInput::make('barcode')->nullable(),
                        TextInput::make('name')->required(),
                        Textarea::make('description')->nullable(),
                        TextInput::make('color')->nullable(),
                        TextInput::make('material')->nullable(),
                    ]),
                Section::make('Product Image')
                    ->schema([
                        FileUpload::make('image')
                            ->image()
                            ->directory('inventory-images')
                            ->nullable(),
                    ]),
                Section::make('Pricing & Stock')
                    ->schema([
                        TextInput::make('price')->numeric()->nullable(),
                        TextInput::make('cost')->numeric()->nullable(),
                        TextInput::make('stock')->numeric()->rules(['min:0']),
                        TextInput::make('low_stock_threshold')->numeric()->nullable(),
                    ]),
                Section::make('Dimensions & Weight')
                    ->schema([
                        TextInput::make('length')->numeric()->nullable(),
                        TextInput::make('width')->numeric()->nullable(),
                        TextInput::make('height')->numeric()->nullable(),
                        Select::make('dims_unit')->options(['cm' => 'cm', 'in' => 'in', 'mm' => 'mm', 'ft' => 'ft', 'm' => 'm'])->nullable(),
                        TextInput::make('weight')->numeric()->nullable(),
                        Select::make('weight_unit')->options(['kg' => 'kg', 'lbs' => 'lbs'])->nullable(),
                        Select::make('size')->options(['XS', 'S', 'M', 'L', 'XL', 'XXL', 'XXXL'])->nullable(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table {
        return $table
            ->columns([
                ImageColumn::make('image')->circular()->size(50),
                TextColumn::make('name')->sortable()->searchable(),
                TextColumn::make('sku')->label('SKU')->sortable(),
                TextColumn::make('stock')
                    ->sortable()
                    ->badge()
                    ->color(fn($record) => $record->stock < $record->low_stock_threshold ? 'danger' : 'success'),
                TextColumn::make('price')->money('USD'),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('updateStock')
                    ->label('Update Stock')
                    ->action(fn($records) => $records->each->increment('stock', 10)),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('view')
                    ->label('View Details')
                    ->icon('heroicon-s-eye')
                    ->modalHeading(fn($record) => $record->name)
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->modalContent(fn($record) => view('filament.modals.view-item', ['record' => $record])),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array {
        return [
            //
        ];
    }

    public static function getPages(): array {
        return [
            'index' => Pages\ListInventories::route('/'),
            'create' => Pages\CreateInventory::route('/create'),
            'edit' => Pages\EditInventory::route('/{record}/edit'),
        ];
    }
}
