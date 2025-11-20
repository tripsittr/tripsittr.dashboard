<?php

namespace App\Filament\Artists\Clusters\Commerce\Resources;

use App\Filament\Admin\Forms\Components\Stack;
use App\Filament\Artists\Clusters\Commerce\Resources\OrderResource\Pages;
use App\Models\InventoryItem;
use App\Models\Order;
use App\Services\OrderStatusTransition;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-s-shopping-cart';

    protected static ?string $cluster = \App\Filament\Artists\Clusters\Commerce\Commerce::class;

    protected static ?string $navigationGroup = null;

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Grid::make(12)->schema([
                Section::make('Order Info')
                    ->icon('heroicon-o-document-text')
                    ->description('Basic reference & customer context for this order.')
                    ->schema([
                        Grid::make(12)->schema([
                            TextInput::make('reference')->label('Reference')->columnSpan(4),
                            Select::make('customer_id')->relationship('customer', 'name')->searchable()->preload()->columnSpan(8),
                        ]),
                        Textarea::make('notes')->label('Internal Notes')->rows(3)->columnSpanFull(),
                    ])
                    ->extraAttributes(['class' => 'bg-white/70 dark:bg-gray-900/40 rounded-xl ring-1 ring-gray-200 dark:ring-gray-700 shadow-sm backdrop-blur'])
                    ->columnSpan(8),
                Section::make('Status')
                    ->icon('heroicon-o-check-circle')
                    ->schema([
                        Placeholder::make('status_badge')
                            ->label('Current Status')
                            ->content(function (Forms\Get $get) {
                                $status = $get('status') ?? 'draft';
                                $colors = [
                                    'draft' => 'bg-gray-200 text-gray-800 dark:bg-gray-700 dark:text-gray-100',
                                    'pending' => 'bg-amber-200 text-amber-800 dark:bg-amber-600/30 dark:text-amber-300',
                                    'paid' => 'bg-emerald-200 text-emerald-800 dark:bg-emerald-600/30 dark:text-emerald-300',
                                    'fulfilled' => 'bg-sky-200 text-sky-800 dark:bg-sky-600/30 dark:text-sky-300',
                                    'shipped' => 'bg-indigo-200 text-indigo-800 dark:bg-indigo-600/30 dark:text-indigo-300',
                                    'cancelled' => 'bg-red-200 text-red-800 dark:bg-red-600/30 dark:text-red-300',
                                    'refunded' => 'bg-rose-200 text-rose-800 dark:bg-rose-600/30 dark:text-rose-300',
                                    'partial' => 'bg-purple-200 text-purple-800 dark:bg-purple-600/30 dark:text-purple-300',
                                ];
                                $cls = $colors[$status] ?? $colors['draft'];

                                return new HtmlString('<span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full '.$cls.'">'.ucfirst($status).'</span>');
                            }),
                        Select::make('status')
                            ->label('Update Status')
                            ->options([
                                'draft' => 'Draft', 'pending' => 'Pending', 'paid' => 'Paid', 'fulfilled' => 'Fulfilled', 'shipped' => 'Shipped', 'cancelled' => 'Cancelled', 'refunded' => 'Refunded', 'partial' => 'Partial',
                            ])->required()->default('draft')->native(false),
                    ])
                    ->extraAttributes(['class' => 'bg-white/70 dark:bg-gray-900/40 rounded-xl ring-1 ring-gray-200 dark:ring-gray-700 shadow-sm flex flex-col gap-2'])
                    ->columnSpan(4),

                Section::make('Shipping')
                    ->icon('heroicon-o-truck')
                    ->description('Destination & contact details. Use the search to auto-fill.')
                    ->schema([
                        Grid::make(12)->schema([
                            TextInput::make('shipping_first_name')->label('First Name')->required()->columnSpan(6),
                            TextInput::make('shipping_last_name')->label('Last Name')->required()->columnSpan(6),
                            TextInput::make('shipping_company')->label('Company')->columnSpan(4),
                            TextInput::make('shipping_email')->email()->label('Email')->columnSpan(4),
                            TextInput::make('shipping_phone')->label('Phone')->columnSpan(4),
                        ]),
                        Select::make('shipping_address_lookup')
                            ->label('Search Address')
                            ->placeholder('Start typing an address...')
                            ->searchable()
                            ->dehydrated(false)
                            ->getSearchResultsUsing(function (string $search) {
                                $search = trim($search);
                                if (strlen($search) < 4) {
                                    return [];
                                }

                                return Cache::remember('ship_addr_suggest:'.md5($search), 300, function () use ($search) {
                                    try {
                                        $response = Http::withHeaders(['User-Agent' => config('app.name').' shipping-address-autocomplete'])->get('https://nominatim.openstreetmap.org/search', ['q' => $search, 'format' => 'json', 'addressdetails' => 1, 'limit' => 8]);
                                        if (! $response->ok()) {
                                            return [];
                                        } $results = $response->json();
                                    } catch (\Throwable $e) {
                                        return [];
                                    }
                                    $mapped = [];
                                    foreach ($results as $r) {
                                        $addr = $r['address'] ?? [];
                                        $line1 = trim(($addr['house_number'] ?? '').' '.($addr['road'] ?? '')) ?: ($addr['road'] ?? ($r['display_name'] ?? ''));
                                        $city = $addr['city'] ?? $addr['town'] ?? $addr['village'] ?? $addr['hamlet'] ?? null;
                                        $region = $addr['state'] ?? $addr['region'] ?? null;
                                        $postal = $addr['postcode'] ?? null;
                                        $countryCode = strtoupper($addr['country_code'] ?? '');
                                        $payload = base64_encode(json_encode(['line1' => $line1, 'city' => $city, 'region' => $region, 'postal_code' => $postal, 'country' => $countryCode]));
                                        $label = Str::limit($r['display_name'] ?? $line1, 80);
                                        $mapped[$payload] = $label;
                                    }

                                    return $mapped;
                                });
                            })
                            ->getOptionLabelUsing(function ($value) {
                                if (! $value) {
                                    return null;
                                } $d = json_decode(base64_decode($value), true);
                                if (! is_array($d)) {
                                    return null;
                                }

                                return implode(', ', array_filter([$d['line1'] ?? null, $d['city'] ?? null, $d['region'] ?? null, $d['postal_code'] ?? null, $d['country'] ?? null]));
                            })
                            ->afterStateUpdated(function (Set $set, ?string $state) {
                                if (! $state) {
                                    return;
                                } $data = json_decode(base64_decode($state), true);
                                if (! is_array($data)) {
                                    return;
                                } $country = strtoupper($data['country'] ?? '');
                                $regionValue = $data['region'] ?? null;
                                if ($country && $regionValue) {
                                    $regions = config('regions.'.$country, []);
                                    if (isset($regions[$regionValue])) {
                                        $data['region'] = $regionValue;
                                    } else {
                                        foreach ($regions as $code => $name) {
                                            if (strcasecmp($name, $regionValue) === 0) {
                                                $data['region'] = $code;
                                                break;
                                            }
                                        }
                                    }
                                } $map = ['line1' => 'shipping_address_line1', 'city' => 'shipping_city', 'region' => 'shipping_region', 'postal_code' => 'shipping_postal_code', 'country' => 'shipping_country'];
                                foreach ($map as $src => $dest) {
                                    if (! empty($data[$src])) {
                                        $set($dest, $data[$src]);
                                    }
                                }
                            }),
                        Grid::make(12)->schema([
                            TextInput::make('shipping_address_line1')->label('Address Line 1')->required()->columnSpan(6),
                            TextInput::make('shipping_address_line2')->label('Address Line 2')->columnSpan(6),
                            TextInput::make('shipping_city')->label('City')->required()->columnSpan(3),
                            Select::make('shipping_country')->label('Country')->options(fn () => config('countries.list', ['US' => 'United States', 'CA' => 'Canada', 'GB' => 'United Kingdom']))->reactive()->required()->columnSpan(3),
                            Select::make('shipping_region')->label('State / Province')->options(fn (Forms\Get $get) => config('regions.'.strtoupper($get('shipping_country')), []))->searchable()->required(fn (Forms\Get $get) => in_array(strtoupper($get('shipping_country')), ['US', 'CA', 'AU']))->placeholder(fn (Forms\Get $get) => $get('shipping_country') ? 'Select region' : 'Select country first')->columnSpan(3),
                            TextInput::make('shipping_postal_code')->label('Postal Code')->required()->columnSpan(3),
                            Select::make('shipping_carrier')
                                ->label('Carrier')
                                ->options([
                                    'fedex' => 'FedEx',
                                    'ups' => 'UPS',
                                    'usps' => 'USPS',
                                    'dhl' => 'DHL',
                                    'canadapost' => 'Canada Post',
                                    'royalmail' => 'Royal Mail',
                                ])
                                ->reactive()
                                ->searchable()
                                ->placeholder('Select carrier')
                                ->columnSpan(4),
                            Select::make('shipping_method')
                                ->label('Service')
                                ->options(function (Forms\Get $get) {
                                    return match ($get('shipping_carrier')) {
                                        'fedex' => [
                                            'FEDEX_GROUND' => 'Ground',
                                            'FIRST_OVERNIGHT' => 'First Overnight',
                                            'PRIORITY_OVERNIGHT' => 'Priority Overnight',
                                            'STANDARD_OVERNIGHT' => 'Standard Overnight',
                                            'FEDEX_2_DAY' => '2 Day',
                                            'FEDEX_2_DAY_AM' => '2 Day AM',
                                            'FEDEX_HOME_DELIVERY' => 'Home Delivery',
                                        ],
                                        'ups' => [
                                            'GROUND' => 'Ground',
                                            '2ND_DAY_AIR' => '2nd Day Air',
                                            'NEXT_DAY_AIR' => 'Next Day Air',
                                            'NEXT_DAY_AIR_SAVER' => 'Next Day Air Saver',
                                            'WORLDWIDE_EXPRESS' => 'Worldwide Express',
                                        ],
                                        'usps' => [
                                            'PRIORITY' => 'Priority Mail',
                                            'PRIORITY_EXPRESS' => 'Priority Mail Express',
                                            'FIRST_CLASS' => 'First-Class',
                                            'GROUND_ADVANTAGE' => 'Ground Advantage',
                                        ],
                                        'dhl' => [
                                            'EXPRESS_WORLDWIDE' => 'Express Worldwide',
                                            'EXPRESS_12' => 'Express 12:00',
                                            'ECONOMY_SELECT' => 'Economy Select',
                                        ],
                                        'canadapost' => [
                                            'EXPEDITED' => 'Expedited Parcel',
                                            'XPRESSPOST' => 'Xpresspost',
                                            'PRIORITY' => 'Priority',
                                        ],
                                        'royalmail' => [
                                            'RM24' => 'Tracked 24',
                                            'RM48' => 'Tracked 48',
                                            'SP1' => 'Special Delivery 1pm',
                                            'SD9' => 'Special Delivery 9am',
                                        ],
                                        default => []
                                    };
                                })
                                ->disabled(fn (Forms\Get $get) => ! $get('shipping_carrier'))
                                ->searchable()
                                ->placeholder(fn (Forms\Get $get) => $get('shipping_carrier') ? 'Select service' : 'Select carrier first')
                                ->reactive()
                                ->columnSpan(4),
                            Toggle::make('shipping_saturday_delivery')
                                ->label('Saturday Delivery')
                                ->visible(fn (Forms\Get $get) => $get('shipping_carrier') === 'fedex')
                                ->inline(false)
                                ->columnSpan(2),
                            TextInput::make('tracking_number')->label('Tracking Number')->columnSpan(4),
                            TextInput::make('shipping_cost')->numeric()->label('Shipping Cost')->columnSpan(2),
                        ]),
                    ])
                    ->extraAttributes(['class' => 'bg-white/70 dark:bg-gray-900/40 rounded-xl ring-1 ring-gray-200 dark:ring-gray-700 shadow-sm backdrop-blur'])
                    ->columnSpan(8),

                Stack::make([
                    Section::make('Shipment Refs')
                        ->icon('heroicon-o-hashtag')
                        ->schema([
                            Grid::make(12)->schema([
                                TextInput::make('shipping_reference')
                                    ->label('Primary Ref')
                                    ->default(fn () => 'SR-'.now()->format('Ymd').'-'.strtoupper(Str::random(5)))
                                    ->helperText('Auto-generated if blank.')
                                    ->maxLength(40)
                                    ->columnSpan(12),
                                Toggle::make('enable_second_reference')
                                    ->label('Add second reference')
                                    ->inline(false)
                                    ->reactive()
                                    ->dehydrated(false)
                                    ->afterStateHydrated(function ($state, callable $set, callable $get) {
                                        if ($get('shipping_reference_2')) {
                                            $set('enable_second_reference', true);
                                        }
                                    })
                                    ->columnSpan(12),
                                TextInput::make('shipping_reference_2')
                                    ->label('Secondary Ref')
                                    ->visible(fn (Forms\Get $get) => (bool) $get('enable_second_reference'))
                                    ->maxLength(40)
                                    ->columnSpan(12),
                            ]),
                        ])
                        ->extraAttributes(['class' => 'bg-white/70 dark:bg-gray-900/40 rounded-xl ring-1 ring-gray-200 dark:ring-gray-700 shadow-sm backdrop-blur']),
                    Section::make('Financial Summary')
                        ->icon('heroicon-o-calculator')
                        ->schema([
                            Grid::make(12)->schema([
                                Placeholder::make('subtotal')->label('Subtotal')->content(function (Forms\Get $get) {
                                    $items = $get('items') ?? [];
                                    $sum = 0;
                                    foreach ($items as $i) {
                                        $sum += (float) ($i['line_total'] ?? ((($i['unit_price'] ?? 0) * ($i['quantity'] ?? 1))));
                                    }

                                    return '$'.number_format($sum, 2);
                                })->columnSpan(4),
                                Placeholder::make('shipping_cost_display')->label('Shipping')->content(fn (Forms\Get $get) => '$'.number_format((float) $get('shipping_cost'), 2))->columnSpan(4),
                                Placeholder::make('total_display')->label('Total')->content(function (Forms\Get $get) {
                                    $items = $get('items') ?? [];
                                    $sum = 0;
                                    foreach ($items as $i) {
                                        $sum += (float) ($i['line_total'] ?? ((($i['unit_price'] ?? 0) * ($i['quantity'] ?? 1))));
                                    }
                                    $shipping = (float) ($get('shipping_cost') ?? 0);

                                    return '$'.number_format($sum + $shipping, 2);
                                })->columnSpan(4),
                            ]),
                        ])
                        ->extraAttributes(['class' => 'bg-white/70 dark:bg-gray-900/40 rounded-xl ring-1 ring-gray-200 dark:ring-gray-700 shadow-sm backdrop-blur']),
                ])->columnSpan(4),

                Section::make('Items')
                    ->icon('heroicon-o-list-bullet')
                    ->description('Add or adjust order line items.')
                    ->schema([
                        Repeater::make('items')
                            ->relationship()
                            ->schema([
                                Select::make('inventory_item_id')->label('Inventory Item')
                                    ->options(function () {
                                        $query = InventoryItem::query()->whereNotNull('catalog_item_id'); // Ensure only items linked to a catalog item appear
                                        if ($tenant = \Filament\Facades\Filament::getTenant()) {
                                            $query->where('team_id', $tenant->id);
                                        }

                                        return $query->get()->mapWithKeys(fn ($inv) => [$inv->id => $inv->variant_label]);
                                    })
                                    ->searchable()->reactive()
                                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                        $item = InventoryItem::find($state);
                                        if ($item) {
                                            $set('description', $item->variant_label);
                                            $set('unit_price', $item->price ?? 0);
                                            $set('catalog_item_id', $item->catalog_item_id);
                                            // Immediately compute line total
                                            $qty = $get('quantity') ?? 1;
                                            $price = $item->price ?? 0;
                                            $set('line_total', $qty * $price);
                                        } else {
                                            $set('line_total', 0);
                                        }
                                    }),
                                TextInput::make('catalog_item_id')->disabled()->dehydrated(),
                                TextInput::make('description')->required()->columnSpan(2),
                                TextInput::make('quantity')->numeric()->default(1)->required()->reactive()->afterStateUpdated(fn ($state, callable $set, callable $get) => $set('line_total', ($get('unit_price') ?? 0) * $state)),
                                TextInput::make('unit_price')->numeric()->reactive()->afterStateUpdated(fn ($state, callable $set, callable $get) => $set('line_total', ($get('quantity') ?? 1) * $state)),
                                TextInput::make('line_total')
                                    ->numeric()
                                    ->disabled()
                                    ->dehydrated()
                                    ->default(0)
                                    ->dehydrateStateUsing(function ($state, callable $get) {
                                        // Guarantee a value if not explicitly set in UI
                                        if ($state === null) {
                                            $qty = $get('quantity') ?? 1;
                                            $unit = $get('unit_price') ?? 0;

                                            return $qty * $unit;
                                        }

                                        return $state;
                                    }),
                            ])->columns(6)
                            ->collapsed(false),
                    ])
                    ->extraAttributes(['class' => 'bg-white/70 dark:bg-gray-900/40 rounded-xl ring-1 ring-gray-200 dark:ring-gray-700 shadow-sm backdrop-blur'])
                    ->columnSpan(12),
            ])->columnSpanFull(),
        ]);

    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('reference')->sortable()->searchable(),
                TextColumn::make('status')->badge()->sortable(),
                TextColumn::make('customer.name')->label('Customer')->sortable()->searchable(),
                TextColumn::make('total')->money('USD'),
                TextColumn::make('created_at')->since()->label('Created'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Action::make('transition')
                    ->label('Change Status')
                    ->icon('heroicon-s-arrow-path')
                    ->form(fn ($record) => [
                        Forms\Components\Select::make('status')
                            ->label('New Status')
                            ->options(collect(OrderStatusTransition::nextOptions($record))->mapWithKeys(fn ($s) => [$s => ucfirst($s)]))
                            ->required(),
                    ])
                    ->visible(fn ($record) => count(OrderStatusTransition::nextOptions($record)) > 0)
                    ->action(function ($record, $data) {
                        $new = $data['status'];
                        if (! OrderStatusTransition::allowed($record, $new)) {
                            throw new \Exception('Illegal transition');
                        }
                        if ($record->status === 'draft' && $new === 'pending') {
                            foreach ($record->items as $item) {
                                if ($inv = $item->inventoryItem) {
                                    $available = $inv->stock - ($inv->reserved ?? 0);
                                    if ($available < $item->quantity) {
                                        throw new \Exception('Insufficient stock for SKU '.$inv->sku);
                                    }
                                }
                            }
                            foreach ($record->items as $item) {
                                if ($inv = $item->inventoryItem) {
                                    $inv->increment('reserved', $item->quantity);
                                    \App\Services\LogActivity::record('inventory.reserve', 'InventoryItem', $inv->id, ['qty' => $item->quantity]);
                                }
                            }
                        }
                        if ($record->status === 'pending' && $new === 'cancelled') {
                            foreach ($record->items as $item) {
                                if ($inv = $item->inventoryItem) {
                                    $inv->decrement('reserved', min($inv->reserved, $item->quantity));
                                    \App\Services\LogActivity::record('inventory.unreserve', 'InventoryItem', $inv->id, ['qty' => $item->quantity]);
                                }
                            }
                        }
                        $record->status = $new;
                        $record->save();
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Action::make('exportCsv')
                        ->label('Export CSV')
                        ->icon('heroicon-s-arrow-down-tray')
                        ->action(function ($records) {
                            $headers = ['Reference', 'Status', 'Customer', 'Total', 'Created'];
                            $lines = [implode(',', $headers)];
                            foreach ($records as $r) {
                                $lines[] = implode(',', [
                                    $r->reference,
                                    $r->status,
                                    str_replace(',', ' ', optional($r->customer)->name),
                                    $r->total,
                                    $r->created_at,
                                ]);
                            }
                            $csv = implode("\n", $lines);

                            return response($csv, 200, [
                                'Content-Type' => 'text/csv',
                                'Content-Disposition' => 'attachment; filename="orders.csv"',
                            ]);
                        }),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    public static function canCreate(): bool
    {
        $user = \Illuminate\Support\Facades\Auth::user();
        if (! $user) {
            return false;
        }
        if ($tenant = \Filament\Facades\Filament::getTenant()) {
            app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($tenant->id);
        }

        return $user->can('create orders') || $user->hasRole('Admin') || $user->hasRole('Manager');
    }
}
