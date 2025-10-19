<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;
    protected static ?string $navigationIcon = 'heroicon-s-user-group';
    protected static ?string $cluster = \App\Filament\Clusters\Commerce\Commerce::class;
    protected static ?string $navigationGroup = null;
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Grid::make(12)->schema([
                Grid::make(8)->schema([
                    Section::make('Customer')
                        ->schema([
                            TextInput::make('name')->required(),
                            TextInput::make('email')->email(),
                            TextInput::make('phone'),
                            TextInput::make('company'),
                        ]),
                ])->columnSpan(8),
                \App\Filament\Forms\Components\Stack::make([
                    Section::make('Address')
                        ->schema([
                            Select::make('address_lookup')
                                ->label('Search Address')
                                ->searchable()
                                ->placeholder('Start typing an address...')
                                ->dehydrated(false)
                                ->getSearchResultsUsing(function(string $search){
                                    $search = trim($search);
                                    if(strlen($search) < 4) return [];
                                    return Cache::remember('addr_suggest:'.md5($search), 300, function() use ($search){
                                        try {
                                            $response = Http::withHeaders([
                                                'User-Agent' => config('app.name').' address-autocomplete'
                                            ])->get('https://nominatim.openstreetmap.org/search', [
                                                'q' => $search,
                                                'format' => 'json',
                                                'addressdetails' => 1,
                                                'limit' => 8,
                                            ]);
                                            if(!$response->ok()) return [];
                                            $results = $response->json();
                                        } catch(\Throwable $e) {
                                            return [];
                                        }
                                        $mapped = [];
                                        foreach($results as $r){
                                            $addr = $r['address'] ?? [];
                                            $line1 = trim(($addr['house_number'] ?? '').' '.($addr['road'] ?? '')) ?: ($addr['road'] ?? ($r['display_name'] ?? ''));
                                            $city = $addr['city'] ?? $addr['town'] ?? $addr['village'] ?? $addr['hamlet'] ?? null;
                                            $region = $addr['state'] ?? $addr['region'] ?? null;
                                            $postal = $addr['postcode'] ?? null;
                                            $countryCode = strtoupper($addr['country_code'] ?? '');
                                            $payload = base64_encode(json_encode([
                                                'line1' => $line1,
                                                'city' => $city,
                                                'region' => $region,
                                                'postal_code' => $postal,
                                                'country' => $countryCode,
                                            ]));
                                            $label = Str::limit($r['display_name'] ?? $line1, 80);
                                            $mapped[$payload] = $label;
                                        }
                                        return $mapped;
                                    });
                                })
                                ->getOptionLabelUsing(function($value){
                                    if(! $value) return null;
                                    $decoded = json_decode(base64_decode($value), true);
                                    if(!is_array($decoded)) return null;
                                    $parts = array_filter([
                                        $decoded['line1'] ?? null,
                                        $decoded['city'] ?? null,
                                        $decoded['region'] ?? null,
                                        $decoded['postal_code'] ?? null,
                                        $decoded['country'] ?? null,
                                    ]);
                                    return implode(', ', $parts);
                                })
                                ->afterStateUpdated(function(Set $set, ?string $state){
                                    if(!$state) return;
                                    $data = json_decode(base64_decode($state), true);
                                    if(!is_array($data)) return;
                                    $country = strtoupper($data['country'] ?? '');
                                    $regionValue = $data['region'] ?? null;
                                    if($country && $regionValue){
                                        $regions = config('regions.'.$country, []);
                                        if(isset($regions[$regionValue])){
                                            $data['region'] = $regionValue;
                                        } else {
                                            $matchedCode = null;
                                            foreach($regions as $code=>$name){
                                                if(strcasecmp($name, $regionValue) === 0){
                                                    $matchedCode = $code; break;
                                                }
                                            }
                                            if($matchedCode) $data['region'] = $matchedCode; else unset($data['region']);
                                        }
                                    }
                                    $map = [
                                        'line1' => 'address_line1',
                                        'city' => 'city',
                                        'region' => 'region',
                                        'postal_code' => 'postal_code',
                                        'country' => 'country',
                                    ];
                                    foreach($map as $src=>$dest){
                                        if(isset($data[$src]) && $data[$src]) $set($dest, $data[$src]);
                                    }
                                }),
                            TextInput::make('address_line1')->label('Address Line 1')->required(),
                            TextInput::make('address_line2')->label('Address Line 2'),
                            TextInput::make('city')->required(),
                            Select::make('country')
                                ->label('Country')
                                ->options(fn() => config('countries.list', [ 'US' => 'United States', 'CA' => 'Canada', 'GB' => 'United Kingdom' ]))
                                ->reactive()
                                ->required(),
                            Select::make('region')
                                ->label('State / Province')
                                ->options(fn(Get $get) => config('regions.'.strtoupper($get('country')), []))
                                ->searchable()
                                ->required(fn(Get $get) => in_array(strtoupper($get('country')), ['US','CA','AU']))
                                ->placeholder(fn(Get $get) => $get('country') ? 'Select region' : 'Select country first'),
                            TextInput::make('postal_code')->label('Postal Code')->required(),
                        ]),
                    Section::make('Notes')
                        ->schema([
                            Textarea::make('notes'),
                        ]),
                ])->columnSpan(4),
            ])
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('email')->sortable(),
                TextColumn::make('phone'),
                TextColumn::make('company')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('city')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')->since()->label('Added'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        if(! $user) return false;
        if($tenant = \Filament\Facades\Filament::getTenant()) {
            app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($tenant->id);
        }
        return $user->can('create customers') || $user->hasRole('Admin') || $user->hasRole('Manager');
    }
}
