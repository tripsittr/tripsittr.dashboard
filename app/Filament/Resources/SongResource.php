<?php

namespace App\Filament\Resources;

use App\Filament\Infolists\Components\ArrayEntry;
use App\Filament\Infolists\Components\AudioEntry;
use App\Filament\Resources\SongResource\Pages;
use App\Models\Song;
use App\Models\Team;
use App\Models\User;
use Closure;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\{
    Card,
    DatePicker,
    Select,
    TextInput,
    Textarea,
    FileUpload,
    Repeater,
    Section
};
use Filament\Forms\Set;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\{
    Grid,
    ImageEntry,
    Section as InfoListSection,
    Split,
    Tabs,
    TextEntry,
    ViewEntry,
};

use Filament\Resources\Resource;

use Filament\Tables;
use Filament\Tables\Columns\{
    TextColumn,
    ImageColumn
};

use Filament\Tables\Table;
use getID3;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SongResource extends Resource {
    protected static ?string $model = Song::class;
    protected static ?int $navigationSort = 2;

    protected static ?string $navigationGroup = 'Music';
    protected static bool $isScopedToTenant = true;

    public static function extractMetadata($filePath, callable $set) {
        if (!$filePath) return;

        $fileFullPath = storage_path("app/public/{$filePath}");

        if (!file_exists($fileFullPath)) return;

        $getID3 = new getID3();
        $fileInfo = $getID3->analyze($fileFullPath);

        // Standard Audio Metadata
        $set('duration', $fileInfo['playtime_seconds'] ?? null);
        $set('bitrate', isset($fileInfo['bitrate']) ? round($fileInfo['bitrate'] / 1000) : null);
        $set('sample_rate', $fileInfo['audio']['sample_rate'] ?? null);
        $set('codec', $fileInfo['audio']['codec'] ?? null);
        $set('format', $fileInfo['fileformat'] ?? null);
        $set('channels', $fileInfo['audio']['channels'] ?? null);

        // Additional Metadata
        $set('file_size', isset($fileInfo['filesize']) ? round($fileInfo['filesize'] / 1048576, 2) : null);
        $set('file_extension', $fileInfo['fileformat'] ?? null);
        $set('bit_depth', $fileInfo['audio']['bits_per_sample'] ?? null);
        $set('compression_ratio', $fileInfo['audio']['compression_ratio'] ?? null);
        $set('encoder', $fileInfo['audio']['encoder'] ?? null);
        $set('channel_mode', $fileInfo['audio']['channelmode'] ?? null);
        $set('mode_extension', $fileInfo['audio']['modeextension'] ?? null);
        $set('audio_data_offset', $fileInfo['audio']['audio_data_offset'] ?? null);
        $set('audio_data_length', $fileInfo['audio']['audio_data_length'] ?? null);
        $set('mime_type', $fileInfo['mime_type'] ?? null);

        // Track & Album Metadata
        $set('track_number', $fileInfo['tags']['id3v2']['track_number'][0] ?? null);
        $set('disc_number', $fileInfo['tags']['id3v2']['part_of_a_set'][0] ?? null);
        $set('album_title', $fileInfo['tags']['id3v2']['album'][0] ?? null);
        $set('year', $fileInfo['tags']['id3v2']['year'][0] ?? null);
        $set('bpm', $fileInfo['tags']['id3v2']['bpm'][0] ?? null);
        $set('mood', $fileInfo['tags']['id3v2']['mood'][0] ?? null);
        $set('key_signature', $fileInfo['tags']['id3v2']['initial_key'][0] ?? null);
        $set('publisher', $fileInfo['tags']['id3v2']['publisher'][0] ?? null);
        $set('copyright', $fileInfo['tags']['id3v2']['copyright_message'][0] ?? null);
        $set('composer_notes', $fileInfo['tags']['id3v2']['composer_notes'][0] ?? null);
        $set('genre_extended', $fileInfo['tags']['id3v2']['genre'][0] ?? null);
        $set('language', $fileInfo['tags']['id3v2']['language'][0] ?? null);

        // Additional Metadata Tags
        $set('album_artist', $fileInfo['tags']['id3v2']['band'][0] ?? null);
        $set('original_release_date', $fileInfo['tags']['id3v2']['original_release_time'][0] ?? null);
        $set('comment', $fileInfo['tags']['id3v2']['comment'][0] ?? null);
        $set('lyrics', $fileInfo['tags']['id3v2']['unsychronized_lyric'][0] ?? null);
        $set('file_owner', $fileInfo['tags']['id3v2']['file_owner'][0] ?? null);
        $set('encoded_by', $fileInfo['tags']['id3v2']['encoded_by'][0] ?? null);
        $set('performer_info', $fileInfo['tags']['id3v2']['performer_info'][0] ?? null);
        $set('conductor', $fileInfo['tags']['id3v2']['conductor'][0] ?? null);
        $set('remixer', $fileInfo['tags']['id3v2']['remixer'][0] ?? null);
        $set('mix_artist', $fileInfo['tags']['id3v2']['mix_artist'][0] ?? null);
        $set('dj_mixer', $fileInfo['tags']['id3v2']['dj_mixer'][0] ?? null);
        $set('author', $fileInfo['tags']['id3v2']['author'][0] ?? null);
        $set('grouping', $fileInfo['tags']['id3v2']['grouping'][0] ?? null);
        $set('subtitle', $fileInfo['tags']['id3v2']['subtitle'][0] ?? null);
    }

    public static function form(Form $form): Form {
        return $form->schema([
            Section::make('Basic Track Details')->schema([
                TextInput::make('title')
                    ->label('Song Title')
                    ->required()
                    ->afterStateUpdated(function (Set $set, $state) {
                        $set('slug', Str::slug($state));
                    })
                    ->helperText('Enter the official title of the song.'),

                TextInput::make('slug')
                    ->label('Slug')
                    ->disabled()
                    ->dehydrated()
                    ->helperText('This is auto-generated from the song title.'),
                Select::make('user_id')
                    ->label('User ID')
                    ->visible(fn(): bool => Auth::user()->type == 'Admin' || Filament::getTenant()->type == 'Admin')
                    ->options(
                        Auth::user()->type == 'Admin'
                            ? User::all()->pluck('name', 'id')
                            : Filament::getTenant()->users->pluck('name', 'id')
                    )
                    ->default(Auth::user()->id)
                    ->required()
                    ->helperText('The ID of the user who uploaded this song.'),
                Select::make('team_id')
                    ->label('Team ID')
                    ->visible(fn(): bool => Auth::user()->type == 'Admin' || Filament::getTenant()->type == 'Admin')
                    ->options(Team::all()->pluck('name', 'id'))
                    ->default(Filament::getTenant()->id)
                    ->required()
                    ->helperText('The ID of the team this song belongs to.'),
                FileUpload::make('song_file')
                    ->label('Upload Song')
                    ->directory('songs' . Filament::getTenant()->id . '/songs/')
                    ->acceptedFileTypes(['audio/mpeg', 'audio/x-mpegurl', 'audio/x-scpls', 'audio/ogg', 'audio/wav', 'audio/flac'])
                    ->downloadable()
                    ->preserveFilenames()
                    ->openable()
                    ->helperText('Upload an MP3, WAV, or FLAC file.'),

                TextInput::make('isrc')->label('ISRC Code')
                    ->helperText('International Standard Recording Code for tracking sales and streams.'),

                TextInput::make('upc')->label('UPC Code')
                    ->helperText('Universal Product Code (UPC) used for album or track identification.'),

                Select::make('genre')
                    ->label('Genre')
                    ->options(self::getGenres())
                    ->searchable()
                    ->helperText('Select the main genre that best fits the song.'),

                Select::make('subgenre')
                    ->label('Subgenre')
                    ->options(self::getSubGenres())
                    ->searchable()
                    ->helperText('Select the subgenre that best fits the song.'),

                FileUpload::make('artwork')
                    ->label('Cover Artwork')
                    ->image()
                    ->directory('artwork' . Auth::user()->id . '/photos/')
                    ->helperText('Upload an image (at least 3000x3000px, JPG/PNG) for distribution.'),
            ]),

            Section::make('Credits & Contributors')->schema([
                Repeater::make('primary_artists')->label('Primary Artists')
                    ->schema([TextInput::make('name')])
                    ->helperText('List all primary artists involved in this track.'),

                Repeater::make('featured_artists')->label('Featured Artists')
                    ->schema([TextInput::make('name')])
                    ->helperText('List all featured artists on this track.'),

                Repeater::make('producers')->label('Producers')
                    ->schema([TextInput::make('name')])
                    ->helperText('List all producers involved in creating this track.'),

                Repeater::make('composers')->label('Composers')
                    ->schema([TextInput::make('name')])
                    ->helperText('List the original composers of this track.'),
            ]),

            Section::make('Release & Distribution')->schema([
                Select::make('album_id')
                    ->label('Album ID')
                    ->relationship('album', 'title')
                    ->preload()
                    ->searchable()
                    ->helperText('Select the album this track belongs to.'),
                DatePicker::make('release_date')->label('Release Date')
                    ->helperText('Date when the track was or will be officially released.'),

                Select::make('status')->label('Release Status')
                    ->options([
                        'unreleased' => 'Unreleased',
                        'scheduled' => 'Scheduled',
                        'released' => 'Released',
                    ])
                    ->default('unreleased')
                    ->helperText('Current status of the track.'),

                Select::make('visibility')->label('Visibility')
                    ->options([
                        'public' => 'Public',
                        'private' => 'Private',
                        'unlisted' => 'Unlisted',
                    ])
                    ->default('private')
                    ->helperText('Who can see this track?'),

                Select::make('distribution_status')->label('Distribution Status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->default('pending')
                    ->helperText('Status of track distribution on streaming platforms.'),
            ]),
        ]);
    }

    public static function table(Table $table): Table {
        return $table
            ->columns([
                ImageColumn::make('artwork')->circular(),
                TextColumn::make('title')->sortable()->searchable(),
                TextColumn::make('genre')->sortable(),
                TextColumn::make('status')->sortable(),
                TextColumn::make('release_date')->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist {
        return $infolist
            ->schema([
                Grid::make(3)
                    ->schema([
                        Grid::make(2)
                            ->columnSpan(2)
                            ->schema([
                                InfoListSection::make('General Information')
                                    ->description('')
                                    ->schema([
                                        TextEntry::make('title')->label('Song Title'),
                                        TextEntry::make('genre')->label('Genre'),
                                        TextEntry::make('subgenre')->label('Subgenre'),
                                        TextEntry::make('isrc')->label('ISRC Code'),
                                        TextEntry::make('upc')->label('UPC Code'),
                                    ])
                                    ->columnSpan(2)
                                    ->columns(3),
                                InfoListSection::make('Track Audio')
                                    ->description('')
                                    ->schema([
                                        AudioEntry::make('song_file')
                                            ->label('Audio Preview'),
                                    ])->columnSpan(2),
                                Tabs::make('Tabs')
                                    ->tabs([
                                        Tabs\Tab::make('Release Information')
                                            ->schema([
                                                TextEntry::make('release_date')->label('Release Date')
                                                    ->badge()
                                                    ->color(fn($record) => $record->release_date->isFuture() ? 'warning' : 'success'),
                                                TextEntry::make('status')->label('Release Status')
                                                    ->badge()
                                                    ->color(fn($record) => match ($record->status) {
                                                        'unreleased' => 'warning',
                                                        'scheduled' => 'info',
                                                        'released' => 'success',
                                                    }),
                                                TextEntry::make('visibility')->label('Visibility')
                                                    ->badge()
                                                    ->color(fn($record) => match ($record->visibility) {
                                                        'public' => 'success',
                                                        'private' => 'warning',
                                                        'unlisted' => 'info',
                                                    }),
                                                TextEntry::make('distribution_status')->label('Distribution Status')
                                                    ->badge()
                                                    ->color(fn($record) => match ($record->distribution_status) {
                                                        'pending' => 'warning',
                                                        'approved' => 'success',
                                                        'rejected' => 'danger',
                                                    }),
                                            ]),
                                        Tabs\Tab::make('Credits & Contributors')
                                            ->schema([
                                                TextEntry::make('primary_artists')->label('Primary Artists')
                                                    ->listWithLineBreaks()
                                                    ->badge()
                                                    ->formatStateUsing(fn($state) => implode('', $state)),
                                                TextEntry::make('featured_artists')->label('Featured Artists')
                                                    ->listWithLineBreaks()
                                                    ->badge()
                                                    ->formatStateUsing(fn($state) => implode('', $state)),
                                                TextEntry::make('producers')->label('Producers')
                                                    ->listWithLineBreaks()
                                                    ->badge()
                                                    ->formatStateUsing(fn($state) => implode('', $state)),
                                                TextEntry::make('composers')->label('Composers')
                                                    ->listWithLineBreaks()
                                                    ->badge()
                                                    ->formatStateUsing(fn($state) => implode('', $state)),
                                            ]),
                                    ])
                                    ->columnSpan(2)
                                    ->columns(4)
                                    ->activeTab(1),
                            ]),
                        InfoListSection::make()
                            ->schema([
                                ImageEntry::make('artwork')->width('100%')->height('auto'),
                            ])
                            ->columnSpan(1),
                    ]),
            ]);
    }

    public static function getPages(): array {
        return [
            'index' => Pages\ListSongs::route('/'),
            'create' => Pages\CreateSong::route('/create'),
            'edit' => Pages\EditSong::route('/{record}/edit'),
            'view' => Pages\ViewSong::route('/{record}'),
        ];
    }

    private static function getGenres(): array {
        return [
            'Alternative' => 'Alternative',
            'Blues' => 'Blues',
            "Children's Music" => "Children's Music",
            'Classical' => 'Classical',
            'Comedy' => 'Comedy',
            'Country' => 'Country',
            'Dance' => 'Dance',
            'Electronic' => 'Electronic',
            'Folk' => 'Folk',
            'Hip Hop (Rap)' => 'Hip Hop (Rap)',
            'Holiday' => 'Holiday',
            'Inspirational' => 'Inspirational',
            'Jazz' => 'Jazz',
            'Latin' => 'Latin',
            'Pop' => 'Pop',
            'Rock' => 'Rock',
        ];
    }

    private static function getSubGenres(): array {
        return [
            'Alternative' => ['grunge' => 'Grunge', 'punk' => 'Punk'],
            'Blues' => ['delta_blues' => 'Delta Blues'],
        ];
    }
}
