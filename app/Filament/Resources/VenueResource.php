<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\Knowledge;
use App\Filament\Resources\VenueResource\Pages;
use App\Filament\Resources\VenueResource\RelationManagers;
use App\Models\Venue;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section as InfolistSection;
use Filament\Infolists\Components\Tabs as InfolistTabs;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\Rules\Exists;

class VenueResource extends Resource {
    protected static ?string $model = Venue::class;
    protected static bool $isScopedToTenant = false;
    protected static ?string $navigationIcon = 'heroicon-s-rectangle-stack';

    protected static ?string $cluster = Knowledge::class;

    public static function form(Form $form): Form {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Venue Name')
                            ->required(),
                        Forms\Components\Textarea::make('address_1')
                            ->label('Address Line 1')
                            ->nullable(),
                        Forms\Components\Textarea::make('address_2')
                            ->label('Address Line 2')
                            ->nullable(),
                        Forms\Components\Select::make('country')
                            ->label('Country')
                            ->options([
                                'US' => 'United States',
                                'CA' => 'Canada',
                                'GB' => 'United Kingdom',
                                'AU' => 'Australia',
                                'DE' => 'Germany',
                                'FR' => 'France',
                                'IN' => 'India',
                                'JP' => 'Japan',
                                'CN' => 'China',
                                'BR' => 'Brazil',
                                'RU' => 'Russia',
                                'MX' => 'Mexico',
                                'IT' => 'Italy',
                                'ES' => 'Spain',
                                'NL' => 'Netherlands',
                                'SE' => 'Sweden',
                                'CH' => 'Switzerland',
                                'NO' => 'Norway',
                                'DK' => 'Denmark',
                                'FI' => 'Finland',
                                'IE' => 'Ireland',
                                'NZ' => 'New Zealand',
                                'ZA' => 'South Africa',
                                'KR' => 'South Korea',
                                'SG' => 'Singapore',
                            ])
                            ->nullable(),
                        Forms\Components\Select::make('state')
                            ->label('State')
                            ->options([
                                'AL' => 'Alabama',
                                'AK' => 'Alaska',
                                'AZ' => 'Arizona',
                                'AR' => 'Arkansas',
                                'CA' => 'California',
                                'CO' => 'Colorado',
                                'CT' => 'Connecticut',
                                'DE' => 'Delaware',
                                'FL' => 'Florida',
                                'GA' => 'Georgia',
                                'HI' => 'Hawaii',
                                'ID' => 'Idaho',
                                'IL' => 'Illinois',
                                'IN' => 'Indiana',
                                'IA' => 'Iowa',
                                'KS' => 'Kansas',
                                'KY' => 'Kentucky',
                                'LA' => 'Louisiana',
                                'ME' => 'Maine',
                                'MD' => 'Maryland',
                                'MA' => 'Massachusetts',
                                'MI' => 'Michigan',
                                'MN' => 'Minnesota',
                                'MS' => 'Mississippi',
                                'MO' => 'Missouri',
                                'MT' => 'Montana',
                                'NE' => 'Nebraska',
                                'NV' => 'Nevada',
                                'NH' => 'New Hampshire',
                                'NJ' => 'New Jersey',
                                'NM' => 'New Mexico',
                                'NY' => 'New York',
                                'NC' => 'North Carolina',
                                'ND' => 'North Dakota',
                                'OH' => 'Ohio',
                                'OK' => 'Oklahoma',
                                'OR' => 'Oregon',
                                'PA' => 'Pennsylvania',
                                'RI' => 'Rhode Island',
                                'SC' => 'South Carolina',
                                'SD' => 'South Dakota',
                                'TN' => 'Tennessee',
                                'TX' => 'Texas',
                                'UT' => 'Utah',
                                'VT' => 'Vermont',
                                'VA' => 'Virginia',
                                'WA' => 'Washington',
                                'WV' => 'West Virginia',
                                'WI' => 'Wisconsin',
                                'WY' => 'Wyoming',
                            ])
                            ->nullable(),
                        Forms\Components\TextInput::make('city')
                            ->label('City')
                            ->nullable(),
                        Forms\Components\TextInput::make('zip')
                            ->label('Zip Code')
                            ->nullable(),
                        Forms\Components\TextInput::make('lat')
                            ->label('Latitude')
                            ->nullable(),
                        Forms\Components\TextInput::make('lng')
                            ->label('Longitude')
                            ->nullable(),
                        Forms\Components\TextInput::make('phone')
                            ->label('Phone Number')
                            ->nullable(),
                        Forms\Components\TextInput::make('email')
                            ->label('Email Address')
                            ->nullable(),
                        Forms\Components\TextInput::make('url')
                            ->label('Website')
                            ->nullable(),
                        Forms\Components\TextInput::make('info_url')
                            ->label('Venue Info Page')
                            ->nullable(),
                        Forms\Components\Select::make('catagories')
                            ->label('Categories')
                            ->multiple()
                            ->options([
                                'Acoustic' => 'Acoustic',
                                'Alternative' => 'Alternative',
                                'Bluegrass' => 'Bluegrass',
                                'Blues' => 'Blues',
                                'Cajun' => 'Cajun',
                                'Zydeco' => 'Zydeco',
                                'Quebecois' => 'Quebecois',
                                'Children\'s Music' => 'Children\'s Music',
                                'Classical' => 'Classical',
                                'Christian Gospel' => 'Christian Gospel',
                                'College' => 'College',
                                'Country' => 'Country',
                                'Electronic' => 'Electronic',
                                'Experimental' => 'Experimental',
                                'Folk' => 'Folk',
                                'Funk' => 'Funk',
                                'LGBTQ' => 'LGBTQ',
                                'Goth' => 'Goth',
                                'Industrial' => 'Industrial',
                                'Hip Hop' => 'Hip Hop',
                                'House Concerts' => 'House Concerts',
                                'Indie' => 'Indie',
                                'Irish/Celtic' => 'Irish/Celtic',
                                'East Coast' => 'East Coast',
                                'Jamband' => 'Jamband',
                                'Jazz' => 'Jazz',
                                'Latin' => 'Latin',
                                'Metal' => 'Metal',
                                'New Age' => 'New Age',
                                'Ambient' => 'Ambient',
                                'Open Mic' => 'Open Mic',
                                'Open Jam' => 'Open Jam',
                                'Pop' => 'Pop',
                                'Prog Rock' => 'Prog Rock',
                                'Psychedelic' => 'Psychedelic',
                                'Punk' => 'Punk',
                                'Hardcore' => 'Hardcore',
                                'R&B Soul' => 'R&B Soul',
                                'Reggae' => 'Reggae',
                                'Ska' => 'Ska',
                                'Rock' => 'Rock',
                                'Rock & Roll' => 'Rock & Roll',
                                'Alt-Country' => 'Alt-Country',
                                'Roots' => 'Roots',
                                'Americana' => 'Americana',
                                'Singer/Songwriter' => 'Singer/Songwriter',
                                'Spoken Word' => 'Spoken Word',
                                'Various Styles/Festival' => 'Various Styles/Festival',
                                'World' => 'World',
                            ])->nullable(),
                        Forms\Components\TextInput::make('capacity')
                            ->label('Capacity')
                            ->nullable(),
                        Forms\Components\Select::make('indoor_outdoor')
                            ->label('Indoor/Outdoor')
                            ->options([
                                'Indoor' => 'Indoor',
                                'Outdoor' => 'Outdoor',
                                'Both' => 'Both',
                            ])->nullable(),
                        Forms\Components\Select::make('seating_type')
                            ->label('Seating Options')
                            ->multiple()
                            ->options([
                                'Standing' => 'Standing',
                                'Seated' => 'Seated',
                                'Assigned Seating' => 'Assigned Seating',
                                'Balcony Standing' => 'Balcony Standing',
                                'Balcony Seated' => 'Balcony Seated',
                                'Private Box' => 'Private Box',
                                'VIP Seating' => 'VIP Seating',
                                'Pit' => 'Pit',
                            ])->nullable(),
                        Forms\Components\Select::make('parking_info')
                            ->label('Parking Information')
                            ->multiple()
                            ->options([
                                'Street Parking' => 'Street Parking',
                                'Parking Lot' => 'Parking Lot',
                                'Parking Garage' => 'Parking Garage',
                                'Valet Parking' => 'Valet Parking',
                                'No Parking Available' => 'No Parking Available',
                            ])->nullable(),
                    ])->columns(2),

                Forms\Components\Section::make('Booking & Contact Info')
                    ->schema([
                        Forms\Components\TextInput::make('booking_contact_name')
                            ->label('Booking Contact Name')
                            ->nullable(),
                        Forms\Components\TextInput::make('booking_email')
                            ->label('Booking Email')
                            ->nullable(),
                        Forms\Components\TextInput::make('booking_phone')
                            ->label('Booking Phone')
                            ->nullable(),
                        Forms\Components\TextInput::make('booking_website')
                            ->label('Booking Website')
                            ->nullable(),
                        Forms\Components\TextInput::make('rental_price_range')
                            ->label('Rental Price Range')
                            ->nullable(),
                    ])->columns(2),

                Forms\Components\Section::make('Technical Specs & Equipment')
                    ->schema([
                        Forms\Components\Toggle::make('sound_equipment_provided')
                            ->label('Sound Equipment Provided')
                            ->nullable(),
                        Forms\Components\Toggle::make('backline_available')
                            ->label('Backline Available')
                            ->nullable(),
                        Forms\Components\Toggle::make('lighting_equipment_provided')
                            ->label('Lighting Equipment Provided')
                            ->nullable(),
                        Forms\Components\Toggle::make('green_room')
                            ->label('Green Room')
                            ->nullable(),
                        Forms\Components\Toggle::make('has_backstage')
                            ->label('Backstage Available')
                            ->nullable(),
                        Forms\Components\Toggle::make('wifi_available')
                            ->label('WiFi Available')
                            ->nullable(),
                        Forms\Components\TextInput::make('stage_size')
                            ->label('Stage Size')
                            ->nullable(),
                    ])->columns(2),

                Forms\Components\Section::make('Accessibility & Additional Services')
                    ->schema([
                        Forms\Components\Toggle::make('wheelchair_accessible')
                            ->label('Wheelchair Accessible')
                            ->nullable(),
                        Forms\Components\Toggle::make('food_beverage_available')
                            ->label('Food & Beverage Available')
                            ->nullable(),
                        Forms\Components\Toggle::make('public_transit_access')
                            ->label('Public Transit Access')
                            ->nullable(),
                        Forms\Components\Select::make('climate_control')
                            ->label('Climate Control')
                            ->multiple()
                            ->options([
                                'Air Conditioning' => 'Air Conditioning',
                                'Heating' => 'Heating',
                                'None' => 'None',
                            ])
                            ->nullable(),
                        Forms\Components\Textarea::make('nearby_hotels')
                            ->label('Nearby Hotels')
                            ->nullable(),
                        Forms\Components\Select::make('restroom_info')
                            ->label('Restroom Details')
                            ->multiple()
                            ->options([
                                'Single Stall' => 'Single Stall',
                                'Multiple Stalls' => 'Multiple Stalls',
                                'Gender Neutral' => 'Gender Neutral',
                                'Family Restroom' => 'Family Restroom',
                                'Accessible Restroom' => 'Accessible Restroom',
                                'Restroom Attendant' => 'Restroom Attendant',
                                'Portable Toilets' => 'Portable Toilets',
                                'Changing Table' => 'Changing Table',
                                'Baby Changing Station' => 'Baby Changing Station',
                                'No Restrooms' => 'No Restrooms',
                            ])
                            ->nullable(),
                        Forms\Components\Textarea::make('notes')
                            ->label('Notes')
                            ->nullable(),
                    ])->columns(2),

                Forms\Components\Section::make('Social Media')
                    ->schema([
                        Forms\Components\TextInput::make('facebook_profile')
                            ->label('Facebook Profile')
                            ->nullable(),
                        Forms\Components\TextInput::make('instagram_handle')
                            ->label('Instagram Handle')
                            ->nullable(),
                        Forms\Components\TextInput::make('linkedin')
                            ->label('LinkedIn')
                            ->nullable(),
                        Forms\Components\TextInput::make('twitter')
                            ->label('Twitter')
                            ->nullable(),
                        Forms\Components\TextInput::make('whatsapp')
                            ->label('WhatsApp')
                            ->nullable(),
                        Forms\Components\TextInput::make('youtube')
                            ->label('YouTube')
                            ->nullable(),
                        Forms\Components\TextInput::make('tiktok')
                            ->label('TikTok')
                            ->nullable(),
                    ])->columns(2),

                Forms\Components\Section::make('Policies')
                    ->schema([
                        Forms\Components\Textarea::make('age_restriction')
                            ->label('Age Policy')
                            ->nullable(),
                        Forms\Components\Textarea::make('alcohol_policy')
                            ->label('Alcohol Policy')
                            ->nullable(),
                        Forms\Components\Textarea::make('bag_policy')
                            ->label('Bag Policy')
                            ->nullable(),
                        Forms\Components\Textarea::make('ticket_policy')
                            ->label('Ticket Policy')
                            ->nullable(),
                        Forms\Components\Select::make('ticket_types')
                            ->label('Ticket Option(s)')
                            ->multiple()
                            ->options([
                                'General Admission' => [
                                    'General Admission (GA)' => 'General Admission (GA)',
                                    'Standing Room Only (SRO)' => 'Standing Room Only (SRO)',
                                ],
                                'Reserved Seating' => [
                                    'Reserved Seating' => 'Reserved Seating',
                                    'Premium Seating' => 'Premium Seating',
                                ],
                                'VIP & Higher' => [
                                    'VIP Pit/VIP GA' => 'VIP Pit/VIP GA',
                                    'Meet & Greet Packages' => 'Meet & Greet Packages',
                                    'Backstage Passes' => 'Backstage Passes',
                                    'Custom Tiers' => 'Custom Tiers',
                                ],
                                'Special Access' => [
                                    'Fan Club/Presale Tickets' => 'Fan Club/Presale Tickets',
                                    'Radio/Sponsor Giveaway Tickets' => 'Radio/Sponsor Giveaway Tickets',
                                ],
                                'Multi/Bundled Tickets' => [
                                    'Group Tickets' => 'Group Tickets',
                                    'Multi-Day Festival Passes' => 'Multi-Day Festival Passes',
                                ],
                                'Purchase & Pickup' => [
                                    'Online Only' => 'Online Only',
                                    'Box Office/Front Door Only' => 'Box Office/Front Door Only',
                                    'Will Call Pickup' => 'Will Call Pickup',
                                ],
                                'Resale & Upgrades' => [
                                    'Night-of-Show Upgrades' => 'Night-of-Show Upgrades',
                                    'Resale/Secondary Market Tickets' => 'Resale/Secondary Market Tickets',
                                ],

                                'Special Discounts' => [
                                    'Student' => 'Student',
                                    'Senior' => 'Senior',
                                    'Military' => 'Military',
                                ],
                            ])->nullable(),
                    ])->columns(2),

                Forms\Components\Section::make('Box Office')
                    ->schema([
                        Forms\Components\TextInput::make('bo_address_1')
                            ->label('Box Office Address Line 1')
                            ->nullable(),
                        Forms\Components\TextInput::make('bo_address_2')
                            ->label('Box Office Address Line 2')
                            ->nullable(),
                        Forms\Components\TextInput::make('bo_city')
                            ->label('Box Office City')
                            ->nullable(),
                        Forms\Components\Select::make('bo_state')
                            ->label('Box Office State')
                            ->options([
                                'AL' => 'Alabama',
                                'AK' => 'Alaska',
                                'AZ' => 'Arizona',
                                'AR' => 'Arkansas',
                                'CA' => 'California',
                                'CO' => 'Colorado',
                                'CT' => 'Connecticut',
                                'DE' => 'Delaware',
                                'FL' => 'Florida',
                                'GA' => 'Georgia',
                                'HI' => 'Hawaii',
                                'ID' => 'Idaho',
                                'IL' => 'Illinois',
                                'IN' => 'Indiana',
                                'IA' => 'Iowa',
                                'KS' => 'Kansas',
                                'KY' => 'Kentucky',
                                'LA' => 'Louisiana',
                                'ME' => 'Maine',
                                'MD' => 'Maryland',
                                'MA' => 'Massachusetts',
                                'MI' => 'Michigan',
                                'MN' => 'Minnesota',
                                'MS' => 'Mississippi',
                                'MO' => 'Missouri',
                                'MT' => 'Montana',
                                'NE' => 'Nebraska',
                                'NV' => 'Nevada',
                                'NH' => 'New Hampshire',
                                'NJ' => 'New Jersey',
                                'NM' => 'New Mexico',
                                'NY' => 'New York',
                                'NC' => 'North Carolina',
                                'ND' => 'North Dakota',
                                'OH' => 'Ohio',
                                'OK' => 'Oklahoma',
                                'OR' => 'Oregon',
                                'PA' => 'Pennsylvania',
                                'RI' => 'Rhode Island',
                                'SC' => 'South Carolina',
                                'SD' => 'South Dakota',
                                'TN' => 'Tennessee',
                                'TX' => 'Texas',
                                'UT' => 'Utah',
                                'VT' => 'Vermont',
                                'VA' => 'Virginia',
                                'WA' => 'Washington',
                                'WV' => 'West Virginia',
                                'WI' => 'Wisconsin',
                                'WY' => 'Wyoming',
                            ])
                            ->nullable(),
                        Forms\Components\TextInput::make('bo_zip')
                            ->label('Box Office Zip Code')
                            ->nullable(),
                        Forms\Components\Select::make('bo_country')
                            ->label('Box Office Country')
                            ->options([
                                'US' => 'United States',
                                'CA' => 'Canada',
                                'GB' => 'United Kingdom',
                                'AU' => 'Australia',
                                'DE' => 'Germany',
                                'FR' => 'France',
                                'IN' => 'India',
                                'JP' => 'Japan',
                                'CN' => 'China',
                                'BR' => 'Brazil',
                                'RU' => 'Russia',
                                'MX' => 'Mexico',
                                'IT' => 'Italy',
                                'ES' => 'Spain',
                                'NL' => 'Netherlands',
                                'SE' => 'Sweden',
                                'CH' => 'Switzerland',
                                'NO' => 'Norway',
                                'DK' => 'Denmark',
                                'FI' => 'Finland',
                                'IE' => 'Ireland',
                                'NZ' => 'New Zealand',
                                'ZA' => 'South Africa',
                                'KR' => 'South Korea',
                                'SG' => 'Singapore',
                            ])
                            ->nullable(),
                        Forms\Components\TextInput::make('bo_phone')
                            ->label('Box Office Phone Number')
                            ->nullable(),
                        Forms\Components\TextInput::make('bo_email')
                            ->label('Box Office Email Address')
                            ->nullable(),
                        Forms\Components\TextInput::make('bo_url')
                            ->label('Box Office Website')
                            ->nullable(),
                        Forms\Components\Textarea::make('bo_hours')
                            ->label('Box Office Hours')
                            ->nullable(),
                        Forms\Components\Textarea::make('bo_notes')
                            ->label('Box Office Notes')
                            ->columnSpanFull()
                            ->nullable(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table {
        return $table
            ->recordUrl(fn(Venue $record): string => route('filament.admin.knowledge.resources.venues.view', ['record' => $record, 'tenant' => Auth::user()->teams->first()->id]),)
            ->columns([
                Stack::make([
                    TextColumn::make('name')->sortable()->searchable()
                        ->description(fn(Venue $record): string => $record->address_1 . ' ' . $record->city . ' ' . $record->state . ' ' . $record->zip),
                    TextColumn::make('phone')->sortable()->searchable(),
                    TextColumn::make('email')->sortable()->searchable(),
                    TextColumn::make('url')->sortable()->searchable(),
                ])
            ])
            ->contentGrid([
                'md' => 2,
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist {
        return $infolist
            ->schema([
                Grid::make(5)
                    ->schema([
                        InfolistSection::make('General Information')
                            ->columnSpan(3)
                            ->schema([
                                TextEntry::make('name')
                                    ->label('Venue Name')
                                    ->icon('heroicon-s-building-office')
                                    ->hidden(fn($record) => is_null($record->name)),
                                TextEntry::make('capacity')
                                    ->label('Capacity')
                                    ->icon('heroicon-s-users')
                                    ->hidden(fn($record) => is_null($record->capacity)),
                                TextEntry::make('indoor_outdoor')
                                    ->label('Indoor/Outdoor')
                                    ->badge()
                                    ->color('primary')
                                    ->icon('heroicon-s-home')
                                    ->hidden(fn($record) => is_null($record->indoor_outdoor)),
                                TextEntry::make('seating_type')
                                    ->label('Seating Options')
                                    ->badge()
                                    ->columnSpanFull()
                                    ->color('primary')
                                    ->icon('heroicon-s-map-pin')
                                    ->hidden(fn($record) => is_null($record->seating_type)),
                                TextEntry::make('parking_info')
                                    ->label('Parking Information')
                                    ->badge()
                                    ->columnSpanFull()
                                    ->color('primary')
                                    ->icon('heroicon-s-map')
                                    ->hidden(fn($record) => is_null($record->parking_info)),
                                TextEntry::make('catagories')
                                    ->label('Categories')
                                    ->badge()
                                    ->columnSpanFull()
                                    ->color('primary')
                                    ->icon('heroicon-s-tag')
                                    ->hidden(fn($record) => is_null($record->catagories)),
                            ])->columns(2),

                        InfolistSection::make('Contact Details')
                            ->columnSpan(2)
                            ->schema([
                                TextEntry::make('phone')
                                    ->label('Phone Number')
                                    ->icon('heroicon-s-phone')
                                    ->hidden(fn($record) => is_null($record->phone)),
                                TextEntry::make('email')
                                    ->label('Email Address')
                                    ->icon('heroicon-s-envelope')
                                    ->hidden(fn($record) => is_null($record->email)),
                                TextEntry::make('url')
                                    ->label('Website')
                                    ->icon('heroicon-s-link')
                                    ->hidden(fn($record) => is_null($record->url)),
                                TextEntry::make('info_url')
                                    ->label('Venue Info Page')
                                    ->icon('heroicon-s-link')
                                    ->hidden(fn($record) => is_null($record->info_url)),
                                TextEntry::make('booking_contact_name')
                                    ->label('Booking Contact Name')
                                    ->icon('heroicon-s-user')
                                    ->hidden(fn($record) => is_null($record->booking_contact_name)),
                                TextEntry::make('booking_email')
                                    ->label('Booking Email')
                                    ->icon('heroicon-s-envelope')
                                    ->hidden(fn($record) => is_null($record->booking_email)),
                                TextEntry::make('booking_phone')
                                    ->label('Booking Phone')
                                    ->icon('heroicon-s-phone')
                                    ->hidden(fn($record) => is_null($record->booking_phone)),
                                TextEntry::make('booking_website')
                                    ->label('Booking Website')
                                    ->icon('heroicon-s-link')
                                    ->hidden(fn($record) => is_null($record->booking_website)),
                                TextEntry::make('rental_price_range')
                                    ->label('Rental Price Range')
                                    ->icon('heroicon-s-currency-dollar')
                                    ->hidden(fn($record) => is_null($record->rental_price_range)),
                            ])->columns(1),

                        InfolistSection::make('Additional Information')
                            ->schema([
                                InfolistTabs::make('Additional Information')
                                    ->tabs([
                                        InfolistTabs\Tab::make('Location Details')
                                            ->schema([
                                                TextEntry::make('address_1')
                                                    ->label('Address Line 1')
                                                    ->icon('heroicon-s-map-pin')
                                                    ->hidden(fn($record) => is_null($record->address_1)),
                                                TextEntry::make('address_2')
                                                    ->label('Address Line 2')
                                                    ->icon('heroicon-s-map-pin')
                                                    ->hidden(fn($record) => is_null($record->address_2)),
                                                TextEntry::make('country')
                                                    ->label('Country')
                                                    ->icon('heroicon-s-globe-alt')
                                                    ->hidden(fn($record) => is_null($record->country)),
                                                TextEntry::make('city')
                                                    ->label('City')
                                                    ->icon('heroicon-s-building-office')
                                                    ->hidden(fn($record) => is_null($record->city)),
                                                TextEntry::make('state')
                                                    ->label('State')
                                                    ->icon('heroicon-s-map')
                                                    ->hidden(fn($record) => is_null($record->state)),
                                                TextEntry::make('zip')
                                                    ->label('Zip Code')
                                                    ->icon('heroicon-s-envelope')
                                                    ->hidden(fn($record) => is_null($record->zip)),
                                                TextEntry::make('lat')
                                                    ->label('Latitude')
                                                    ->icon('heroicon-s-map-pin')
                                                    ->hidden(fn($record) => is_null($record->lat)),
                                                TextEntry::make('lng')
                                                    ->label('Longitude')
                                                    ->icon('heroicon-s-map-pin')
                                                    ->hidden(fn($record) => is_null($record->lng)),
                                            ])->columns(2),

                                        InfolistTabs\Tab::make('Technical Specs & Equipment')
                                            ->schema([
                                                TextEntry::make('stage_size')
                                                    ->label('Stage Size')
                                                    ->icon('heroicon-s-cube-transparent')
                                                    ->hidden(fn($record) => is_null($record->stage_size)),
                                                IconEntry::make('sound_equipment_provided')
                                                    ->boolean()
                                                    ->label('Sound Equipment Provided')
                                                    ->trueIcon('heroicon-s-check-circle')
                                                    ->falseIcon('heroicon-s-x-circle')
                                                    ->trueColor('success')
                                                    ->falseColor('danger')
                                                    ->hidden(fn($record) => is_null($record->sound_equipment_provided)),
                                                IconEntry::make('backline_available')
                                                    ->boolean()
                                                    ->label('Backline Available')
                                                    ->trueIcon('heroicon-s-check-circle')
                                                    ->falseIcon('heroicon-s-x-circle')
                                                    ->trueColor('success')
                                                    ->falseColor('danger')
                                                    ->hidden(fn($record) => is_null($record->backline_available)),
                                                IconEntry::make('has_backstage')
                                                    ->boolean()
                                                    ->label('Backstage Available')
                                                    ->trueIcon('heroicon-s-check-circle')
                                                    ->falseIcon('heroicon-s-x-circle')
                                                    ->trueColor('success')
                                                    ->falseColor('danger')
                                                    ->hidden(fn($record) => is_null($record->has_backstage)),
                                                IconEntry::make('lighting_equipment_provided')
                                                    ->boolean()
                                                    ->label('Lighting Equipment Provided')
                                                    ->trueIcon('heroicon-s-check-circle')
                                                    ->falseIcon('heroicon-s-x-circle')
                                                    ->trueColor('success')
                                                    ->falseColor('danger')
                                                    ->hidden(fn($record) => is_null($record->lighting_equipment_provided)),
                                                IconEntry::make('green_room')
                                                    ->boolean()
                                                    ->label('Green Room')
                                                    ->trueIcon('heroicon-s-check-circle')
                                                    ->falseIcon('heroicon-s-x-circle')
                                                    ->trueColor('success')
                                                    ->falseColor('danger')
                                                    ->hidden(fn($record) => is_null($record->green_room)),
                                            ])->columns(2),

                                        InfolistTabs\Tab::make('Accessibility & Additional Services')
                                            ->schema([
                                                IconEntry::make('wheelchair_accessible')
                                                    ->boolean()
                                                    ->label('Wheelchair Accessible')
                                                    ->trueIcon('heroicon-s-check-circle')
                                                    ->falseIcon('heroicon-s-x-circle')
                                                    ->trueColor('success')
                                                    ->falseColor('danger')
                                                    ->hidden(fn($record) => is_null($record->wheelchair_accessible)),
                                                IconEntry::make('wifi_available')
                                                    ->boolean()
                                                    ->label('WiFi Available')
                                                    ->trueIcon('heroicon-s-check-circle')
                                                    ->falseIcon('heroicon-s-x-circle')
                                                    ->trueColor('success')
                                                    ->falseColor('danger')
                                                    ->hidden(fn($record) => is_null($record->wifi_available)),
                                                IconEntry::make('food_beverage_available')
                                                    ->boolean()
                                                    ->label('Food & Beverage Available')
                                                    ->trueIcon('heroicon-s-check-circle')
                                                    ->falseIcon('heroicon-s-x-circle')
                                                    ->trueColor('success')
                                                    ->falseColor('danger')
                                                    ->hidden(fn($record) => is_null($record->food_beverage_available)),
                                                IconEntry::make('public_transit_access')
                                                    ->boolean()
                                                    ->label('Public Transit Access')
                                                    ->trueIcon('heroicon-s-check-circle')
                                                    ->falseIcon('heroicon-s-x-circle')
                                                    ->trueColor('success')
                                                    ->falseColor('danger')
                                                    ->hidden(fn($record) => is_null($record->public_transit_access)),
                                                TextEntry::make('climate_control')
                                                    ->label('Climate Control')
                                                    ->badge()
                                                    ->color('primary')
                                                    ->icon('heroicon-s-sun')
                                                    ->hidden(fn($record) => is_null($record->climate_control)),
                                                TextEntry::make('restroom_info')
                                                    ->label('Restroom Details')
                                                    ->badge()
                                                    ->color('primary')
                                                    ->icon('heroicon-s-tag')
                                                    ->hidden(fn($record) => is_null($record->restroom_info)),
                                                TextEntry::make('nearby_hotels')
                                                    ->label('Nearby Hotels')
                                                    ->icon('heroicon-s-building-office')
                                                    ->hidden(fn($record) => is_null($record->nearby_hotels)),
                                                TextEntry::make('notes')
                                                    ->label('Notes')
                                                    ->icon('heroicon-s-document-text')
                                                    ->hidden(fn($record) => is_null($record->notes)),
                                            ])->columns(2),

                                        InfolistTabs\Tab::make('Social Media')
                                            ->schema([
                                                TextEntry::make('facebook_profile')
                                                    ->label('Facebook Profile')
                                                    ->icon('heroicon-s-link')
                                                    ->hidden(fn($record) => is_null($record->facebook_profile)),
                                                TextEntry::make('instagram_handle')
                                                    ->label('Instagram Handle')
                                                    ->icon('heroicon-s-link')
                                                    ->hidden(fn($record) => is_null($record->instagram_handle)),
                                                TextEntry::make('linkedin')
                                                    ->label('LinkedIn')
                                                    ->icon('heroicon-s-link')
                                                    ->hidden(fn($record) => is_null($record->linkedin)),
                                                TextEntry::make('twitter')
                                                    ->label('Twitter')
                                                    ->icon('heroicon-s-link')
                                                    ->hidden(fn($record) => is_null($record->twitter)),
                                                TextEntry::make('whatsapp')
                                                    ->label('WhatsApp')
                                                    ->icon('heroicon-s-link')
                                                    ->hidden(fn($record) => is_null($record->whatsapp)),
                                                TextEntry::make('youtube')
                                                    ->label('YouTube')
                                                    ->icon('heroicon-s-link')
                                                    ->hidden(fn($record) => is_null($record->youtube)),
                                                TextEntry::make('tiktok')
                                                    ->label('TikTok')
                                                    ->icon('heroicon-s-link')
                                                    ->hidden(fn($record) => is_null($record->tiktok)),
                                            ])->columns(2),

                                        InfolistTabs\Tab::make('Policies')
                                            ->schema([
                                                TextEntry::make('age_restriction')
                                                    ->columnSpanFull()
                                                    ->label('Age Policy')
                                                    ->icon('heroicon-s-lock-closed')
                                                    ->hidden(fn($record) => is_null($record->age_restriction)),
                                                TextEntry::make('alcohol_policy')
                                                    ->columnSpanFull()
                                                    ->label('Alcohol Policy')
                                                    ->icon('heroicon-s-identification')
                                                    ->hidden(fn($record) => is_null($record->alcohol_policy)),
                                                TextEntry::make('bag_policy')
                                                    ->columnSpanFull()
                                                    ->label('Bag Policy')
                                                    ->icon('heroicon-s-shopping-bag')
                                                    ->hidden(fn($record) => is_null($record->bag_policy)),
                                                TextEntry::make('ticket_policy')
                                                    ->columnSpanFull()
                                                    ->label('Ticket Policy')
                                                    ->icon('heroicon-s-ticket')
                                                    ->hidden(fn($record) => is_null($record->ticket_policy)),
                                                TextEntry::make('ticket_types')
                                                    ->columnSpanFull()
                                                    ->label('Ticket Option(s)')
                                                    ->badge()
                                                    ->color('primary')
                                                    ->icon('heroicon-s-ticket')
                                                    ->hidden(fn($record) => is_null($record->ticket_types)),
                                            ]),

                                        InfolistTabs\Tab::make('Box Office')
                                            ->schema([
                                                TextEntry::make('bo_address_1')
                                                    ->label('Address')
                                                    ->icon('heroicon-s-map-pin')
                                                    ->hidden(fn($record) => is_null($record->bo_address_1)),
                                                TextEntry::make('bo_address_2')
                                                    ->label('Address 2')
                                                    ->icon('heroicon-s-map-pin')
                                                    ->hidden(fn($record) => is_null($record->bo_address_2)),
                                                TextEntry::make('bo_city')
                                                    ->label('City')
                                                    ->icon('heroicon-s-building-office')
                                                    ->hidden(fn($record) => is_null($record->bo_city)),
                                                TextEntry::make('bo_state')
                                                    ->label('State')
                                                    ->icon('heroicon-s-map')
                                                    ->hidden(fn($record) => is_null($record->bo_state)),
                                                TextEntry::make('bo_zip')
                                                    ->label('Box Office Zip Code')
                                                    ->icon('heroicon-s-envelope')
                                                    ->hidden(fn($record) => is_null($record->bo_zip)),
                                                TextEntry::make('bo_country')
                                                    ->label('Country')
                                                    ->icon('heroicon-s-globe-alt')
                                                    ->hidden(fn($record) => is_null($record->bo_country)),
                                                TextEntry::make('bo_phone')
                                                    ->label('Box Office Phone Number')
                                                    ->icon('heroicon-s-phone')
                                                    ->hidden(fn($record) => is_null($record->bo_phone)),
                                                TextEntry::make('bo_email')
                                                    ->label('Box Office Email Address')
                                                    ->icon('heroicon-s-envelope')
                                                    ->hidden(fn($record) => is_null($record->bo_email)),
                                                TextEntry::make('bo_url')
                                                    ->label('Box Office Website')
                                                    ->icon('heroicon-s-link')
                                                    ->hidden(fn($record) => is_null($record->bo_url)),
                                                TextEntry::make('bo_hours')
                                                    ->label('Box Office Hours')
                                                    ->icon('heroicon-s-clock')
                                                    ->hidden(fn($record) => is_null($record->bo_hours)),
                                                TextEntry::make('bo_notes')
                                                    ->label('Notes')
                                                    ->icon('heroicon-s-pencil-square')
                                                    ->columnSpanFull()
                                                    ->hidden(fn($record) => is_null($record->bo_notes)),
                                            ])->columns(2),
                                    ]),
                            ]),
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
            'index' => Pages\ListVenues::route('/'),
            'create' => Pages\CreateVenue::route('/create'),
            'view' => Pages\ViewVenue::route('/{record}'),
            'edit' => Pages\EditVenue::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
