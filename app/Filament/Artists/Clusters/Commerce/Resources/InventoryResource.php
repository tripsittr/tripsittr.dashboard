<?php
namespace App\Filament\Artists\Clusters\Commerce\Resources;

use App\Filament\Artists\Clusters\Commerce\Resources\InventoryResource\Pages;
use App\Models\Inventory;
use App\Models\InventoryItem;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
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
    protected static ?string $cluster = \App\Filament\Artists\Clusters\Commerce\Commerce::class;
    protected static bool $isScopedToTenant = true;

    public static function form(Form $form): Form {
        return $form
            ->schema([
                Grid::make(4)
                    ->schema([
                        Section::make('Product Details')
                            ->schema([
                                Select::make('catalog_item_id')
                                    ->label('Catalog Item')
                                    ->relationship('catalogItem','part_number')
                                    ->searchable()
                                    ->preload()
                                    ->required(),
                                TextInput::make('name')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->afterStateHydrated(fn($set,$record)=>$record? $set('name',$record->catalogItem?->name):null)
                                    ->label('Name (from Catalog)'),
                                Textarea::make('description')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->afterStateHydrated(fn($set,$record)=>$record? $set('description',$record->catalogItem?->description):null)
                                    ->label('Description (from Catalog)')
                                    ->rows(3),
                                TextInput::make('material')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->afterStateHydrated(fn($set,$record)=>$record? $set('material',$record->catalogItem?->material):null)
                                    ->label('Material (from Catalog)'),
                                // Dynamic variant attribute selects (sizes/colors) derived from CatalogItem
                                Forms\Components\Select::make('color')
                                    ->label('Variant Color')
                                    ->options(function (Get $get) {
                                        $catalogId = $get('catalog_item_id');
                                        if(!$catalogId) return [];
                                        $catalog = \App\Models\CatalogItem::find($catalogId);
                                        $colors = $catalog?->colors ?? [];
                                        if(is_array($colors)) {
                                            return collect($colors)->mapWithKeys(fn($c)=>[$c=>$c])->all();
                                        }
                                        return [];
                                    })
                                    ->searchable()
                                    ->visible(fn(Get $get) => (bool)$get('catalog_item_id') && in_array(\App\Models\CatalogItem::find($get('catalog_item_id'))?->item_type, ['clothing']))
                                    ->required(fn(Get $get) => \App\Models\CatalogItem::find($get('catalog_item_id'))?->item_type === 'clothing')
                                    ->validationMessages(['required' => 'Color is required for clothing variants.'])
                                    ->placeholder('Select color'),
                                Forms\Components\Select::make('size')
                                    ->label('Variant Size')
                                    ->options(function (Get $get) {
                                        $catalogId = $get('catalog_item_id');
                                        if(!$catalogId) return [];
                                        $catalog = \App\Models\CatalogItem::find($catalogId);
                                        $sizes = $catalog?->sizes ?? [];
                                        if(is_array($sizes)) {
                                            return collect($sizes)->mapWithKeys(fn($s)=>[$s=>$s])->all();
                                        }
                                        return [];
                                    })
                                    ->searchable()
                                    ->visible(fn(Get $get) => (bool)$get('catalog_item_id') && in_array(\App\Models\CatalogItem::find($get('catalog_item_id'))?->item_type, ['clothing']))
                                    ->required(fn(Get $get) => \App\Models\CatalogItem::find($get('catalog_item_id'))?->item_type === 'clothing')
                                    ->validationMessages(['required' => 'Size is required for clothing variants.'])
                                    ->placeholder('Select size'),
                                // Equipment specific override (warranty months display)
                                Forms\Components\TextInput::make('warranty_display')
                                    ->label('Warranty (months)')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->visible(fn(Get $get) => (bool)$get('catalog_item_id') && \App\Models\CatalogItem::find($get('catalog_item_id'))?->item_type === 'equipment')
                                    ->afterStateHydrated(function($set,$record){
                                        if($record?->catalogItem?->warranty_months){
                                            $set('warranty_display', $record->catalogItem->warranty_months);
                                        }
                                    }),
                                // Media specific (format, runtime) preview
                                Forms\Components\TextInput::make('format_preview')
                                    ->label('Format')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->visible(fn(Get $get) => (bool)$get('catalog_item_id') && \App\Models\CatalogItem::find($get('catalog_item_id'))?->item_type === 'media')
                                    ->afterStateHydrated(function($set,$record){
                                        if($record?->catalogItem?->format){ $set('format_preview',$record->catalogItem->format);} }),
                                Forms\Components\TextInput::make('runtime_preview')
                                    ->label('Runtime (min)')
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->visible(fn(Get $get) => (bool)$get('catalog_item_id') && \App\Models\CatalogItem::find($get('catalog_item_id'))?->item_type === 'media')
                                    ->afterStateHydrated(function($set,$record){
                                        if($record?->catalogItem?->runtime_minutes){ $set('runtime_preview',$record->catalogItem->runtime_minutes);} }),
                            ])->columnSpan(3),
                        Section::make('Product Codes')
                            ->description('Product codes can be generated from the action menu. A barcode can be generated once a SKU and Batch have been entered and saved.')
                            ->schema([
                                TextInput::make('sku')->required(),
                                TextInput::make('batch_number')->nullable(),
                                TextInput::make('serial_number')->nullable(),
                                TextInput::make('barcode')->nullable(),
                            ])->columnSpan(1),
                    ]),
                Section::make('Product Image')
                    ->schema([
                        FileUpload::make('image')
                            ->image()
                            ->directory('inventory-images')
                            ->nullable(),
                    ]),
                Tabs::make()
                    ->schema([
                        Tab::make('Pricing & Stock')
                            ->schema([
                                Grid::make()
                                    ->schema([
                                        Forms\Components\Toggle::make('override_price')
                                            ->inline(false)
                                            ->reactive()
                                            ->afterStateUpdated(fn($state, callable $set) => !$state ? $set('price_override', null) : null)
                                            ->label('Override Price'),
                                        TextInput::make('price_override')
                                            ->numeric()
                                            ->reactive()
                                            ->visible(fn(Get $get) => $get('override_price'))
                                            ->label('Custom Price'),
                                        Forms\Components\Toggle::make('override_cost')
                                            ->inline(false)
                                            ->reactive()
                                            ->afterStateUpdated(fn($state, callable $set) => !$state ? $set('cost_override', null) : null)
                                            ->label('Override Cost'),
                                        TextInput::make('cost_override')
                                            ->numeric()
                                            ->reactive()
                                            ->visible(fn(Get $get) => $get('override_cost'))
                                            ->label('Custom Cost'),
                                        TextInput::make('stock')->numeric()->rules(['min:0'])->required(),
                                        TextInput::make('low_stock_threshold')->numeric()->nullable()->required()
                                            ->hintIcon('heroicon-m-question-mark-circle', tooltip: 'When your stock reaches this level, you will be notified.'),
                                    ]),
                                Forms\Components\Placeholder::make('effective_price')
                                    ->label('Effective Price')
                                    ->content(fn($record, Get $get) => $get('override_price') && $get('price_override') !== null
                                        ? number_format((float)$get('price_override'),2)
                                        : ($record?->catalogItem?->default_price !== null ? number_format($record->catalogItem->default_price,2) : '—')
                                    ),
                                Forms\Components\Placeholder::make('effective_cost')
                                    ->label('Effective Cost')
                                    ->content(fn($record, Get $get) => $get('override_cost') && $get('cost_override') !== null
                                        ? number_format((float)$get('cost_override'),2)
                                        : ($record?->catalogItem?->default_cost !== null ? number_format($record->catalogItem->default_cost,2) : '—')
                                    ),
                            ]),
                        Tab::make('Dimensions & Weight')
                            ->schema([
                                Grid::make()
                                    ->schema([
                                        Forms\Components\Placeholder::make('length')
                                            ->label('Length')
                                            ->content(fn($record) => $record?->catalogItem?->length),
                                        Forms\Components\Placeholder::make('width')
                                            ->label('Width')
                                            ->content(fn($record) => $record?->catalogItem?->width),
                                        Forms\Components\Placeholder::make('height')
                                            ->label('Height')
                                            ->content(fn($record) => $record?->catalogItem?->height),
                                        Forms\Components\Placeholder::make('dims_unit')
                                            ->label('Dims Unit')
                                            ->content(fn($record) => $record?->catalogItem?->dims_unit),
                                        Forms\Components\Placeholder::make('weight')
                                            ->label('Weight')
                                            ->content(fn($record) => $record?->catalogItem?->weight),
                                        Forms\Components\Placeholder::make('weight_unit')
                                            ->label('Weight Unit')
                                            ->content(fn($record) => $record?->catalogItem?->weight_unit),
                                    ]),
                            ]),
                    ])->columnSpanFull(),
            ]);
    }


    public static function table(Table $table): Table {
        return $table
            ->columns([
                ImageColumn::make('image')->circular()->size(50),
                TextColumn::make('variant_label')
                    ->label('Item')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('catalogItem.item_type')
                    ->label('Type')
                    ->badge()
                    ->sortable()
                    ->color(fn($state) => match($state) {
                        'clothing' => 'info',
                        'media' => 'purple',
                        'equipment' => 'warning',
                        'accessory' => 'success',
                        'generic' => 'gray',
                        default => 'secondary'
                    })
                    ->formatStateUsing(fn($state) => $state ? ucfirst(str_contains($state,'(') ? $state : $state) : '—'),
                TextColumn::make('sku')->label('SKU')->sortable(),
                TextColumn::make('status')->badge()->colors([
                    'success' => 'in_stock',
                    'warning' => 'reserved',
                    'danger' => 'damaged',
                ])->label('Status'),
                TextColumn::make('stock')
                    ->sortable()
                    ->badge()
                    ->color(fn($record) => $record->stock < $record->low_stock_threshold ? 'danger' : 'success'),
                TextColumn::make('reserved')->label('Reserved')->sortable()->badge()->color('warning'),
                TextColumn::make('price')->money('USD'),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('adjustStock')
                    ->label('Adjust Stock')
                    ->form([
                        Forms\Components\TextInput::make('amount')->numeric()->required()->label('Adjustment Amount (use negative to reduce)')
                    ])
                    ->action(function ($records, array $data) {
                        foreach ($records as $rec) {
                            $rec->increment('stock', (int) $data['amount']);
                        }
                    })
                    ->color('warning')
                    ->deselectRecordsAfterCompletion(),
            ])
            ->filters([
                Tables\Filters\Filter::make('low_stock')
                    ->label('Low Stock')
                    ->query(fn(Builder $query) => $query->whereColumn('stock', '<', 'low_stock_threshold')),
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
                    Tables\Actions\BulkAction::make('exportCsv')
                        ->label('Export CSV')
                        ->icon('heroicon-s-arrow-down-tray')
                        ->action(function($records){
                            $headers = ['Name','SKU','Status','Stock','Reserved','Price'];
                            $lines = [implode(',', $headers)];
                            foreach($records as $r){
                                $lines[] = implode(',', [
                                    str_replace(',', ' ', $r->name),
                                    $r->sku,
                                    $r->status,
                                    $r->stock,
                                    $r->reserved,
                                    $r->price,
                                ]);
                            }
                            $csv = implode("\n", $lines);
                            return response($csv,200,[
                                'Content-Type'=>'text/csv',
                                'Content-Disposition'=>'attachment; filename="inventory.csv"'
                            ]);
                        })
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

    public static function canCreate(): bool
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        if(! $user) return false;
        if($tenant = \Filament\Facades\Filament::getTenant()) {
            app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($tenant->id);
        }
        return $user->can('create inventory items') || $user->hasRole('Admin') || $user->hasRole('Manager');
    }
}
