<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CatalogItemResource\Pages;
use App\Models\CatalogItem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CatalogItemResource extends Resource
{
    protected static ?string $model = CatalogItem::class;
    protected static ?string $navigationIcon = 'heroicon-s-rectangle-stack';
    protected static ?string $cluster = \App\Filament\Clusters\Commerce\Commerce::class;
    protected static ?string $navigationGroup = null;
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        $materials = [
            'cotton'=>'Cotton','polyester'=>'Polyester','wool'=>'Wool','leather'=>'Leather','denim'=>'Denim','silk'=>'Silk','nylon'=>'Nylon','metal'=>'Metal','plastic'=>'Plastic','wood'=>'Wood','paper'=>'Paper','vinyl'=>'Vinyl',
        ];
        $colors = [
            'black'=>'Black','white'=>'White','gray'=>'Gray','red'=>'Red','blue'=>'Blue','green'=>'Green','yellow'=>'Yellow','purple'=>'Purple','orange'=>'Orange','pink'=>'Pink','brown'=>'Brown'
        ];
        $clothingSizes = ['XS'=>'XS','S'=>'S','M'=>'M','L'=>'L','XL'=>'XL','XXL'=>'XXL','XXXL'=>'XXXL'];
        $itemTypes = [
            'generic'=>'Generic',
            'clothing'=>'Clothing',
            'media'=>'Media (Album)',
            'equipment'=>'Equipment',
            'accessory'=>'Accessory',
        ];
        return $form->schema([
            Grid::make(12)->schema([
                // Main column
                Grid::make(8)->schema([
                    Section::make('Identification')
                        ->collapsible()
                        ->collapsed(false)
                        ->schema([
                            Select::make('item_type')->options($itemTypes)->reactive()->required()->default('generic'),
                            TextInput::make('part_number')->required()->unique(ignoreRecord: true),
                            TextInput::make('reference_code'),
                            TextInput::make('name')->required(),
                            Textarea::make('description')->columnSpanFull(),
                        ]),
                    Section::make('Dimensions & Weight')
                        ->collapsible()
                        ->collapsed()
                        ->schema([
                            TextInput::make('length')->numeric(),
                            TextInput::make('width')->numeric(),
                            TextInput::make('height')->numeric(),
                            Select::make('dims_unit')->options(['cm'=>'cm','in'=>'in','mm'=>'mm','ft'=>'ft','m'=>'m']),
                            TextInput::make('weight')->numeric(),
                            Select::make('weight_unit')->options(['kg'=>'kg','lbs'=>'lbs']),
                        ])->visible(fn(\Filament\Forms\Get $get) => in_array($get('item_type'), ['generic','equipment']))
                        ->columnSpanFull(),
                    Section::make('Clothing Specific')
                        ->collapsible()
                        ->collapsed(fn(\Filament\Forms\Get $get) => $get('item_type') !== 'clothing')
                        ->schema([
                            Select::make('sizes')
                                ->options($clothingSizes)
                                ->multiple()
                                ->placeholder('Select available sizes')
                                ->rules(function(\Filament\Forms\Get $get){
                                    return $get('item_type') === 'clothing'
                                        ? ['required','array','min:1']
                                        : ['nullable'];
                                })
                                ->validationMessages([
                                    'required' => 'At least one size is required for clothing items.',
                                    'array' => 'Sizes must be an array.',
                                    'min' => 'Select at least one size.'
                                ]),
                            Select::make('colors')
                                ->options($colors)
                                ->multiple()
                                ->placeholder('Available colors')
                                ->helperText('Optional; choose color variants for this clothing item'),
                        ])->visible(fn(\Filament\Forms\Get $get) => $get('item_type') === 'clothing')
                        ->columnSpanFull(),
                    Section::make('Media Specific')
                        ->collapsible()
                        ->collapsed(fn(\Filament\Forms\Get $get) => $get('item_type') !== 'media')
                        ->schema([
                            Select::make('format')->options(['cd'=>'CD','vinyl'=>'Vinyl','digital'=>'Digital'])->label('Format'),
                            TextInput::make('runtime_minutes')->numeric()->label('Runtime (minutes)'),
                        ])->visible(fn(\Filament\Forms\Get $get) => $get('item_type') === 'media')
                        ->columnSpanFull(),
                    Section::make('Equipment Specific')
                        ->collapsible()
                        ->collapsed(fn(\Filament\Forms\Get $get) => $get('item_type') !== 'equipment')
                        ->schema([
                            TextInput::make('warranty_months')->numeric()->label('Warranty (months)'),
                        ])->visible(fn(\Filament\Forms\Get $get) => $get('item_type') === 'equipment')
                        ->columnSpanFull(),
                ])->columnSpan(8),

                // Sidebar stack
                \App\Filament\Forms\Components\Stack::make([
                    Section::make('Pricing & Base')
                        ->collapsible()
                        ->collapsed()
                        ->schema([
                            Select::make('material')
                                ->options(array_merge($materials, ['__other' => 'Other (specify)']))
                                ->searchable()
                                ->reactive()
                                ->afterStateUpdated(function($state, callable $set){
                                    if($state !== '__other') {
                                        $set('material_other', null);
                                    }
                                }),
                            Forms\Components\TextInput::make('material_other')
                                ->label('Custom Material')
                                ->reactive()
                                ->afterStateUpdated(function($state, callable $set){
                                    if($state){
                                        $set('material', $state);
                                    }
                                })
                                ->visible(fn(\Filament\Forms\Get $get) => $get('material') === '__other'),
                            TextInput::make('brand'),
                            TextInput::make('default_cost')->numeric(),
                            TextInput::make('default_price')->numeric(),
                            TextInput::make('default_lead_time_days')->numeric()->label('Lead Time (days)'),
                        ]),
                    Section::make('Notes')
                        ->collapsible()
                        ->collapsed()
                        ->schema([
                            Textarea::make('notes')->rows(4),
                        ]),
                ])->columnSpan(4),
            ])
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('part_number')->searchable()->sortable(),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('material')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('brand')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('default_price')->money('USD'),
                TextColumn::make('updated_at')->since()->label('Updated'),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCatalogItems::route('/'),
            'create' => Pages\CreateCatalogItem::route('/create'),
            'edit' => Pages\EditCatalogItem::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        if(! $user) return false;
        if($tenant = \Filament\Facades\Filament::getTenant()) {
            app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($tenant->id);
        }
        return $user->can('create catalog items') || $user->hasRole('Admin') || $user->hasRole('Manager');
    }
}
