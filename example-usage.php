<?php

namespace App\Filament\Resources\ExampleResource\Pages;

use App\Filament\Resources\ExampleResource;
use App\Infolists\Components\AudioEntry;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Resources\Pages\ViewRecord;

class ViewExample extends ViewRecord
{
    protected static string $resource = ExampleResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Audio Examples')
                    ->description('Various configurations of the custom audio player')
                    ->schema([
                        // Basic usage
                        AudioEntry::make('basic_audio')
                            ->label('Basic Audio Player')
                            ->state('https://www.soundjay.com/misc/sounds/bell-ringing-05.wav'),

                        // Customized player
                        AudioEntry::make('custom_audio')
                            ->label('Customized Audio Player')
                            ->state('https://www.soundjay.com/misc/sounds/bell-ringing-05.wav')
                            ->defaultVolume(0.7)
                            ->defaultSpeed(1.25)
                            ->primaryColor('#10b981') // emerald-500
                            ->backgroundColor('#f3f4f6'), // gray-100

                        // Minimal player (limited controls)
                        AudioEntry::make('minimal_audio')
                            ->label('Minimal Audio Player')
                            ->state('https://www.soundjay.com/misc/sounds/bell-ringing-05.wav')
                            ->speedControl(false)
                            ->defaultVolume(0.5),

                        // Full-featured player
                        AudioEntry::make('full_audio')
                            ->label('Full-Featured Audio Player')
                            ->state('https://www.soundjay.com/misc/sounds/bell-ringing-05.wav')
                            ->autoplay(false) // Note: autoplay is usually blocked by browsers
                            ->controls(true)
                            ->timeDisplay(true)
                            ->volumeControl(true)
                            ->speedControl(true)
                            ->defaultVolume(0.8)
                            ->defaultSpeed(1.0)
                            ->format('wav'),

                        // Audio with dynamic state
                        AudioEntry::make('dynamic_audio')
                            ->label('Dynamic Audio URL')
                            ->state(function ($record) {
                                // Return audio URL based on record data
                                return $record->audio_url ?? 'https://www.soundjay.com/misc/sounds/bell-ringing-05.wav';
                            }),
                    ]),

                Section::make('Additional Information')
                    ->schema([
                        TextEntry::make('title')
                            ->label('Title'),
                        
                        TextEntry::make('description')
                            ->label('Description')
                            ->columnSpanFull(),
                    ])
            ]);
    }
}