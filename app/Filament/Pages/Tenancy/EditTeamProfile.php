<?php

namespace App\Filament\Pages\Tenancy;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Tenancy\EditTenantProfile;

class EditTeamProfile extends EditTenantProfile {
    public static function getLabel(): string {
        return 'Team profile';
    }

    public function form(Form $form): Form {
        return $form
            ->schema([
                TextInput::make('name'),
                FileUpload::make('team_avatar')
                    ->image()
                    ->avatar(),
                DatePicker::make('formation_date')->label('Formation Date')->nullable(),
                Select::make('genre')
                    ->label('Genres')
                    ->multiple()
                    ->options([
                        'Drum & Bass' => 'Drum & Bass',
                        'Dubstep' => 'Dubstep',
                        'Grime' => 'Grime',
                        'Jersey Club' => 'Jersey Club',
                        'Jungle' => 'Jungle',
                        'Acid House' => 'Acid House',
                        'Afro House' => 'Afro House',
                        'Afrobeats' => 'Afrobeats',
                        'Amapiano' => 'Amapiano',
                        'Deep House' => 'Deep House',
                        'Disco' => 'Disco',
                        'Garage' => 'Garage',
                        'Hardstyle' => 'Hardstyle',
                        'House' => 'House',
                        'Minimal' => 'Minimal',
                        'Progressive House' => 'Progressive House',
                        'Psytrance' => 'Psytrance',
                        'Slap House' => 'Slap House',
                        'Tech House' => 'Tech House',
                        'Techno' => 'Techno',
                        'Trance' => 'Trance',
                        'Ambient' => 'Ambient',
                        'Chill-Out' => 'Chill-Out',
                        'Downtempo' => 'Downtempo',
                        'Electro' => 'Electro',
                        'IDM' => 'IDM',
                        'Trip Hop' => 'Trip Hop',
                        'Boom Bap' => 'Boom Bap',
                        'Drill' => 'Drill',
                        'Lo-Fi' => 'Lo-Fi',
                        'Phonk' => 'Phonk',
                        'Reggaeton' => 'Reggaeton',
                        'R&B' => 'R&B',
                        'Trap' => 'Trap',
                        'West Coast' => 'West Coast',
                        'African' => 'African',
                        'Asian' => 'Asian',
                        'Bossa Nova' => 'Bossa Nova',
                        'Brazilian' => 'Brazilian',
                        'Caribbean' => 'Caribbean',
                        'Cuban' => 'Cuban',
                        'Dancehall' => 'Dancehall',
                        'Indian' => 'Indian',
                        'Latin American' => 'Latin American',
                        'Middle Eastern' => 'Middle Eastern',
                        'Reggae' => 'Reggae',
                        'Blues' => 'Blues',
                        'Classic R&B' => 'Classic R&B',
                        'Classical' => 'Classical',
                        'Country' => 'Country',
                        'Folk' => 'Folk',
                        'Funk' => 'Funk',
                        'Gospel' => 'Gospel',
                        'Indie Rock' => 'Indie Rock',
                        'Jazz' => 'Jazz',
                        'Metal' => 'Metal',
                        'Post-Punk' => 'Post-Punk',
                        'Punk' => 'Punk',
                        'Rock' => 'Rock',
                        'Soul' => 'Soul',
                        'EDM' => 'EDM',
                        'Electropop' => 'Electropop',
                        'Future House' => 'Future House',
                        'Hyperpop' => 'Hyperpop',
                        'K-pop' => 'K-pop',
                        'Moombahton' => 'Moombahton',
                        'Pop' => 'Pop',
                        'Synthwave' => 'Synthwave',
                        'Tropical House' => 'Tropical House',
                        'Cinematic' => 'Cinematic',
                        'Video Game' => 'Video Game',
                    ])
                    ->searchable()
                    ->nullable()
                    ->preload(),

                Section::make('Social Media & Contact')
                    ->schema([
                        TextInput::make('website')->label('Website')->url()->placeholder('https://teamwebsite.com')->nullable(),
                        TextInput::make('instagram')->label('Instagram')->prefix('https://instagram.com/')->placeholder('handle')->nullable(),
                        TextInput::make('twitter')->label('Twitter/X')->prefix('https://twitter.com/')->placeholder('handle')->nullable(),
                        TextInput::make('facebook')->label('Facebook')->prefix('https://facebook.com/')->placeholder('page')->nullable(),
                        TextInput::make('youtube')->label('YouTube Channel')->prefix('https://youtube.com/')->placeholder('channel')->nullable(),
                        TextInput::make('email')->label('Contact Email')->email()->placeholder('contact@mail.com')->nullable(),
                        TextInput::make('phone')->label('Contact Phone')->mask('(999) 999-9999')->nullable(),
                    ])->collapsible(),
            ]);
    }
}
