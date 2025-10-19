<?php

namespace App\Filament\Clusters\Music\Resources;

use App\Filament\Clusters\Music\Resources\TracksResource\Pages;
use App\Models\Song;
use App\Models\Team;
use App\Models\User;
use App\Services\Audio\AudioMetadataExtractor;
use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section as InfoListSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TracksResource extends Resource
{
    protected static ?string $model = Song::class;

    protected static ?int $navigationSort = 2;

    protected static ?string $cluster = \App\Filament\Clusters\Music\Music::class;

    protected static ?string $navigationIcon = 'heroicon-s-musical-note';

    protected static ?string $navigationLabel = 'Tracks';

    protected static bool $isScopedToTenant = true;

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTracks::route('/'),
            'create' => Pages\CreateTrack::route('/create'),
            'edit' => Pages\EditTrack::route('/{record}/edit'),
            'view' => Pages\ViewTrack::route('/{record}'),
            'verify' => Pages\VerifyTrack::route('/{record}/verify'),
        ];
    }

    public static function extractMetadata($filePath, callable $set)
    {
        if (! $filePath) {
            return;
        }
        $full = storage_path("app/public/{$filePath}");
        if (! file_exists($full)) {
            return;
        }

        $service = app(AudioMetadataExtractor::class);
        $all = $service->extract($full);

        $core = $all['core'];
        $summary = $all['tag_summary'];

        $set('duration', $core['duration_seconds'] ?? null);
        $set('bitrate', $core['bitrate_kbps'] ?? null);
        $set('sample_rate', $core['sample_rate'] ?? null);
        $set('codec', $core['codec'] ?? null);
        $set('format', $core['format'] ?? null);
        $set('channels', $core['channels'] ?? null);
        $set('bitrate_mode', $core['bitrate_mode'] ?? null);
        $set('compression_ratio', $core['compression_ratio'] ?? null);
        $set('encoder', $core['encoder'] ?? null);
        $set('file_size', isset($core['filesize']) ? round($core['filesize'] / 1048576, 2) : null);
        $set('mime_type', $core['mime_type'] ?? null);
        $set('track_number', $core['track_number'] ?? null);
        $set('track_total', $core['track_total'] ?? null);
        $set('disc_number', $core['disc_number'] ?? null);
        $set('disc_total', $core['disc_total'] ?? null);
        $set('md5_file', $core['md5_file'] ?? null);

        $set('album_title', $summary['album'] ?? null);
        $set('year', $summary['year'] ?? null);
        $set('bpm', $summary['bpm'] ?? null);
        $set('mood', $summary['mood'] ?? null);
        $set('key_signature', $summary['key'] ?? null);
        $set('publisher', $summary['publisher'] ?? null);
        $set('copyright', $summary['copyright'] ?? null);
        $set('genre_extended', $summary['genre'] ?? null);
        $set('language', $summary['language'] ?? null);
        $set('album_artist', $summary['album_artist'] ?? null);
        $set('comment', $summary['comment'] ?? null);
        $set('lyrics', $summary['lyrics'] ?? null);
        $set('grouping', $summary['grouping'] ?? null);
        $set('subtitle', $summary['subtitle'] ?? null);

        $set('replay_gain_track', $all['replay_gain']['track_gain'] ?? null);
        $set('replay_gain_album', $all['replay_gain']['album_gain'] ?? null);
        $set('raw_metadata', $all['raw'] ?? null);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Basic Track Details')->schema([
                TextInput::make('title')
                    ->label('Song Title')
                    ->required()
                    ->afterStateUpdated(function (Set $set, $state) {
                        $set('slug', Str::slug($state));
                    })
                    ->helperText('Enter the official title of the song.')
                    ->disabled(fn ($record) => $record && $record->album && in_array($record->album->status, ['in_review', 'approved'])),
                TextInput::make('slug')
                    ->label('Slug')
                    ->disabled()
                    ->dehydrated()
                    ->helperText('This is auto-generated from the song title.'),
                Select::make('user_id')
                    ->label('User ID')
                    ->visible(fn (): bool => Auth::user()->type == 'Admin' || Filament::getTenant()->type == 'Admin')
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
                    ->visible(fn (): bool => Auth::user()->type == 'Admin' || Filament::getTenant()->type == 'Admin')
                    ->options(Team::all()->pluck('name', 'id'))
                    ->default(Filament::getTenant()->id)
                    ->required()
                    ->helperText('The ID of the team this song belongs to.'),
                FileUpload::make('song_file')
                    ->label('Upload Song')
                    ->directory(fn () => 'songs/'.Filament::getTenant()->id.'/songs/')
                    ->acceptedFileTypes([
                        'audio/mpeg',
                        'audio/x-mpegurl',
                        'audio/x-scpls',
                        'audio/ogg',
                        'audio/wav',
                        'audio/wave',
                        'audio/x-wav',
                        'audio/vnd.wave',
                        'audio/flac',
                        'audio/x-flac',
                    ])
                    ->downloadable()
                    ->maxSize(262144)
                    ->rules([
                        'file',
                        'max:262144',
                        'mimetypes:audio/mpeg,audio/ogg,audio/wav,audio/wave,audio/x-wav,audio/vnd.wave,audio/flac,audio/x-flac',
                    ])
                    ->preserveFilenames()
                    ->openable()
                    ->helperText('Upload an MP3, WAV, or FLAC file.')
                    ->disabled(fn ($record) => $record && $record->album && in_array($record->album->status, ['in_review', 'approved']))
                    ->afterStateUpdated(function ($state, callable $set, $record) {
                        if ($state) {
                            self::extractMetadata($state, function ($k, $v) use ($set) {
                                $set($k, $v);
                            });
                            if ($record) {
                                foreach (['duration', 'bitrate', 'bitrate_mode', 'sample_rate', 'codec', 'format', 'channels', 'file_size', 'mime_type', 'track_number', 'track_total', 'disc_number', 'disc_total', 'replay_gain_track', 'replay_gain_album', 'md5_file', 'raw_metadata'] as $field) {
                                    if (array_key_exists($field, $record->getAttributes())) {
                                        continue;
                                    }
                                }
                                $record->save();
                            }
                        }
                    }),
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
                    ->directory('artwork'.Auth::user()->id.'/photos/')
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('artwork')->circular(),
                TextColumn::make('title')->sortable()->searchable(),
                TextColumn::make('genre')->sortable(),
                TextColumn::make('status')->sortable(),
                TextColumn::make('release_date')->sortable(),
                TextColumn::make('file_status')
                    ->label('File')
                    ->badge()
                    ->state(fn (Song $record) => (function () use ($record) {
                        $rel = $record->normalizedSongFile();

                        return ($rel && Storage::disk('public')->exists($rel)) ? 'OK' : 'Missing';
                    })())
                    ->color(fn (string $state) => $state === 'OK' ? 'success' : 'danger'),
            ])
            ->filters([
                \Filament\Tables\Filters\Filter::make('missing_file')
                    ->label('Missing file (likely)')
                    ->query(fn ($query) => $query
                        ->whereNull('song_file')
                        ->orWhere('song_file', '=', '')
                        ->orWhere('song_file', 'like', 'demo/%')
                    ),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('validate')
                    ->label('Validate')
                    ->icon('heroicon-o-shield-check')
                    ->color('primary')
                    ->action(function (Song $record) {
                        [$ok, $issues] = self::validateForRelease($record);
                        if ($ok) {
                            Notification::make()
                                ->title('Validation passed')
                                ->body('This track has enough info for a full release.')
                                ->success()
                                ->send();
                        } else {
                            $body = 'Please fix the following before release:'."\n\n".collect($issues)->map(fn ($i) => '• '.$i)->implode("\n");
                            Notification::make()
                                ->title('Validation failed')
                                ->body($body)
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Grid::make(3)
                    ->schema([
                        Grid::make(1)
                            ->columnSpan(2)
                            ->schema([
                                InfoListSection::make('Audio')
                                    ->schema([
                                        \App\Filament\Infolists\Components\AudioEntry::make('song_file')
                                            ->label('Audio Preview'),
                                    ])
                                    ->columns(1),
                                \Filament\Infolists\Components\ViewEntry::make('details_header')
                                    ->view('filament.infolists.song-details')
                                    ->columnSpanFull(),
                                InfoListSection::make('Credits')
                                    ->schema([
                                        TextEntry::make('featured_artists')->label('Featured Artists')
                                            ->listWithLineBreaks()
                                            ->badge()
                                            ->formatStateUsing(fn ($state) => self::normalizeNameArray($state)),
                                        TextEntry::make('producers')->label('Producers')
                                            ->listWithLineBreaks()
                                            ->badge()
                                            ->formatStateUsing(fn ($state) => self::normalizeNameArray($state)),
                                        TextEntry::make('composers')->label('Composers')
                                            ->listWithLineBreaks()
                                            ->badge()
                                            ->formatStateUsing(fn ($state) => self::normalizeNameArray($state)),
                                    ])
                                    ->columns(3),
                            ]),
                        InfoListSection::make()
                            ->schema([
                                ImageEntry::make('artwork')->width('100%')->height('auto'),
                            ])
                            ->columnSpan(1),
                    ]),
            ]);
    }

    private static function getStatusColor(?string $status): string
    {
        return match ($status) {
            'unreleased' => 'warning',
            'scheduled' => 'info',
            'released' => 'success',
            'draft' => 'gray',
            'none', null, '' => 'secondary',
            default => 'secondary',
        };
    }

    private static function getVisibilityColor(?string $visibility): string
    {
        return match ($visibility) {
            'public' => 'success',
            'private' => 'warning',
            'unlisted' => 'info',
            'none', null, '' => 'secondary',
            default => 'secondary',
        };
    }

    private static function getDistributionStatusColor(?string $distributionStatus): string
    {
        return match ($distributionStatus) {
            'pending' => 'warning',
            'approved' => 'success',
            'rejected' => 'danger',
            'none', null, '' => 'secondary',
            default => 'secondary',
        };
    }

    private static function normalizeNameArray($state): string
    {
        if (is_array($state)) {
            if (! empty($state) && is_array($state[0] ?? null) && array_key_exists('name', $state[0])) {
                $state = array_map(fn ($row) => $row['name'], $state);
            }
            $flat = [];
            array_walk_recursive($state, function ($v) use (&$flat) {
                $flat[] = $v;
            });
            $names = array_filter(array_map('trim', $flat));

            return implode("\n", $names);
        }
        if (is_string($state)) {
            $decoded = json_decode($state, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return self::normalizeNameArray($decoded);
            }

            return trim($state);
        }

        return '';
    }

    private static function getGenres(): array
    {
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

    private static function getSubGenres(): array
    {
        return [
            'Alternative' => ['grunge' => 'Grunge', 'punk' => 'Punk'],
            'Blues' => ['delta_blues' => 'Delta Blues'],
        ];
    }

    public static function validateForRelease(Song $record): array
    {
        $issues = [];
        if (! filled($record->title)) {
            $issues[] = 'Title is missing';
        }
        $rel = method_exists($record, 'normalizedSongFile') ? $record->normalizedSongFile() : $record->song_file;
        $hasFile = $rel && Storage::disk('public')->exists($rel);
        if (! $hasFile) {
            $issues[] = 'Audio file is missing or not found on disk';
        }
        if (! filled($record->artwork)) {
            $issues[] = 'Artwork is missing';
        }
        $names = self::normalizeNameArray($record->primary_artists ?? null);
        if (trim($names) === '') {
            $issues[] = 'At least one primary artist is required';
        }
        if (! filled($record->genre)) {
            $issues[] = 'Genre is required';
        }
        if (empty($record->release_date)) {
            $issues[] = 'Release date is required';
        }
        if (! is_numeric($record->duration) || (float) $record->duration <= 0) {
            $issues[] = 'Duration is missing or invalid';
        }
        if (! filled($record->isrc)) {
            $issues[] = 'ISRC is required for track distribution';
        }

        return [empty($issues), $issues];
    }
}
