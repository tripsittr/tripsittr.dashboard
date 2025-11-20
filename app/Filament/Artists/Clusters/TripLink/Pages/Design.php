<?php

namespace App\Filament\Artists\Clusters\TripLink\Pages;

use App\Filament\Artists\Clusters\TripLink\TripLink as TripLinkCluster;
use App\Models\TripLink as TripLinkModel;
use Filament\Forms;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Design extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $cluster = TripLinkCluster::class;

    protected static ?string $navigationLabel = 'Header & Design';

    protected static ?string $title = 'Header & Design';

    protected static ?string $navigationIcon = 'heroicon-s-swatch';

    protected static string $view = 'filament.clusters.triplink.pages.design';

    public array $data = [];

    public function mount(): void
    {
        $teamId = optional(Auth::user()?->current_team)->id ?? null;
        // Don't create a DB record just to render the form — fetch if exists, otherwise
        // prepare an unsaved model instance. Creating without a slug would fail because
        // the `slug` column is non-nullable in the migration.
        $trip = TripLinkModel::where('team_id', $teamId ?? 0)->first() ?? new TripLinkModel(['team_id' => $teamId ?? 0]);

        $state = [
            'title' => $trip->title ?? null,
            'slug' => $trip->slug ?? null,
            'bio' => $trip->bio ?? null,
            'profile_image' => $trip->profile_image ?? null,
            'banner_image' => $trip->banner_image ?? null,
            'design' => $trip->design ?? [],
            'published' => (bool) ($trip->published ?? false),
        ];

        $this->form->fill($state);
    }

    protected function getFormSchema(): array
    {
        return [
            Grid::make(2)->schema([
                TextInput::make('title')->required()->maxLength(120),
                TextInput::make('slug')->maxLength(80)->helperText('Custom URL slug (optional)'),
                FileUpload::make('profile_image')
                    ->image()
                    ->disk('public')
                    ->directory(fn () => 'triplinks/'.(optional(Auth::user()?->current_team)->id ?? 'unknown')),
                FileUpload::make('banner_image')
                    ->image()
                    ->disk('public')
                    ->directory(fn () => 'triplinks/'.(optional(Auth::user()?->current_team)->id ?? 'unknown')),
            ]),
            Textarea::make('bio')->rows(4)->columnSpanFull(),
            ColorPicker::make('design.background_color')
                ->label('Background Color')
                ->default('#ffffff')
                ->helperText('Hex color for page background')
                ->columnSpanFull(),
            Grid::make(2)->schema([
                ColorPicker::make('design.accent_color')
                    ->label('Primary / Accent Color')
                    ->default('#f87171')
                    ->helperText('Used for buttons and accents'),
                ColorPicker::make('design.text_color')
                    ->label('Text Color')
                    ->default('#111827')
                    ->helperText('Main text color'),
            ]),
            TextInput::make('design.avatar_size')
                ->label('Avatar size (px)')
                ->numeric()
                ->default(120)
                ->helperText('Default profile avatar size in pixels. Admins can adjust this to scale the avatar.'),
            TextInput::make('design.hero_height')
                ->label('Hero height (px)')
                ->numeric()
                ->default(200)
                ->helperText('Height of the banner/hero area in pixels.'),
            Toggle::make('published')->label('Published')->columnSpanFull(),
        ];
    }

    public function submit(): void
    {
        $teamId = optional(Auth::user()?->current_team)->id ?? null;
        // Use firstOrNew so we can generate and assign a valid slug before the model is
        // persisted. firstOrCreate would attempt an insert immediately and fail because
        // `slug` is required by the DB schema.
        $trip = TripLinkModel::firstOrNew(['team_id' => $teamId ?? 0]);

        $state = $this->form->getState();

        $trip->title = $state['title'] ?? null;
        // Auto-generate slug if blank
        $teamName = optional(Auth::user()?->current_team)->name ?? null;
        $incoming = trim((string) ($state['slug'] ?? ''));
        if ($incoming === '') {
            $base = Str::slug($state['title'] ?? $teamName ?? ('team-'.($teamId ?? '0')));
        } else {
            $base = Str::slug($incoming);
        }

        // Ensure uniqueness (ignore current record)
        $candidate = $base;
        $i = 1;
        while (TripLinkModel::where('slug', $candidate)->where('id', '<>', $trip->id)->exists()) {
            $candidate = $base.'-'.$i++;
        }
        $trip->slug = $candidate;
        $trip->bio = $state['bio'] ?? null;
        // Handle file uploads securely; Filament may provide UploadedFile or storage path
        if (! empty($state['profile_image'])) {
            $avatarSize = intval($state['design']['avatar_size'] ?? 120);
            $trip->profile_image = $this->processImageValue($state['profile_image'], $teamId, 'profile', $avatarSize);
        }
        if (! empty($state['banner_image'])) {
            $trip->banner_image = $this->processImageValue($state['banner_image'], $teamId, 'banner');
        }

        // design is an array; keep existing keys and merge
        $existingDesign = $trip->design ?? [];
        $incomingDesign = $state['design'] ?? [];

        $trip->design = array_merge($existingDesign, $incomingDesign ?: []);

        $trip->published = (bool) ($state['published'] ?? false);

        $trip->save();

        Notification::make()->title('Design saved')->success()->send();
        $this->redirect(static::getUrl(), navigate: true);
    }

    protected function getFormStatePath(): ?string
    {
        return 'data';
    }

    /**
     * Process an uploaded image value returned by Filament FileUpload.
     * Accepts UploadedFile or a storage path string. Returns stored path relative to the public disk.
     */
    private function processImageValue(mixed $value, ?int $teamId, string $type, int $size = 400): ?string
    {
        // If it's already a public storage path and exists, return as-is
        if (is_string($value)) {
            $str = ltrim($value, '/');
            if (filter_var($value, FILTER_VALIDATE_URL)) {
                // External URL - return as provided
                return $value;
            }
            if (Storage::disk('public')->exists($str)) {
                return $str;
            }

            // Unknown string - return as-is
            return $value;
        }

        if ($value instanceof UploadedFile) {
            $ext = strtolower($value->getClientOriginalExtension() ?: 'jpg');
            $filename = now()->format('YmdHis').'_'.Str::random(8).'.'.$ext;
            $directory = 'triplinks/'.($teamId ?? 'unknown');
            $path = $directory.'/'.$filename;

            // If Intervention Image is available, perform resizing before storage.
            // Newer Intervention versions (v3+) expose ImageManager instead of the old
            // ImageManagerStatic helper. Detect either API and adapt accordingly.
            if (class_exists('Intervention\\Image\\ImageManagerStatic') || class_exists('Intervention\\Image\\ImageManager')) {
                try {
                    if (class_exists('Intervention\\Image\\ImageManagerStatic')) {
                        $factory = 'Intervention\\Image\\ImageManagerStatic';
                        $img = $factory::make($value->getRealPath());
                    } else {
                        // Intervention v3+: use ImageManager and read the file
                        $manager = \Intervention\Image\ImageManager::gd();
                        $img = $manager->read($value->getRealPath());
                    }

                    if ($type === 'profile') {
                        // Resize to requested avatar size (square). Try fit(), then cover(), then fallback.
                        $s = max(24, min(1200, intval($size)));
                        if (method_exists($img, 'fit')) {
                            $img->fit($s, $s);
                        } elseif (method_exists($img, 'cover')) {
                            $img->cover($s, $s);
                        } else {
                            $img->resize($s, $s);
                        }
                    } else {
                        // banner: max width 1600, maintain aspect ratio
                        if (method_exists($img, 'resize')) {
                            $img->resize(1600, null, function ($constraint) {
                                $constraint->aspectRatio();
                                $constraint->upsize();
                            });
                        }
                    }

                    $encoded = (string) $img->encode('jpg', 85);
                    Storage::disk('public')->put($path, $encoded);

                    return $path;
                } catch (\Throwable $e) {
                    // On failure, fallback to storing original file
                }
            }

            // Default: store the uploaded file as-is
            Storage::disk('public')->putFileAs($directory, $value, $filename);

            return $path;
        }

        return null;
    }
}
