<?php

namespace App\Filament\Artists\Clusters\TripLink\Pages;

use App\Filament\Artists\Clusters\TripLink\TripLink as TripLinkCluster;
use App\Models\TripLink as TripLinkModel;
use Filament\Forms;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
// EdgeEditor component removed — using simple TextInput fields for padding/margin now.
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class Layout extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $cluster = TripLinkCluster::class;

    protected static ?string $navigationLabel = 'Elements & Layout';

    protected static ?string $title = 'Elements & Layout';

    protected static ?string $slug = 'elements';

    protected static ?string $navigationIcon = 'heroicon-s-code-bracket-square';

    protected static string $view = 'filament.clusters.triplink.pages.layout';

    public array $data = [];

    public function mount(): void
    {
        $teamId = optional(Auth::user()?->current_team)->id ?? null;
        $trip = TripLinkModel::where('team_id', $teamId ?? 0)->first() ?? new TripLinkModel(['team_id' => $teamId ?? 0]);

        $existing = $trip->layout ?? [];
        if (! is_array($existing)) {
            $existing = [];
        }

        $normalized = [];
        foreach ($existing as $item) {
            if (! is_array($item)) {
                continue;
            }

            $type = $item['type'] ?? null;
            // Map legacy types to current ones
            if (in_array($type, ['link', 'single_button'], true)) {
                $item['type'] = 'button';
            }
            if (in_array($type, ['text', 'bio', 'contact'], true)) {
                $item['type'] = 'paragraph';
            }

            $normalized[] = $item;
        }

        $this->form->fill([
            'layout' => $normalized,
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            Repeater::make('layout')
                ->label('Sections')
                ->collapsed()
                ->createItemButtonLabel('Add section')
                ->schema([
                    Grid::make(2)->schema([
                        Select::make('type')
                            ->label('Section type')
                            ->options([
                                'title' => 'Title',
                                'paragraph' => 'Paragraph',
                                'image' => 'Image',
                                'gallery' => 'Gallery',
                                'button' => 'Button',
                            ])
                            ->default('paragraph')
                            ->reactive(),

                        TextInput::make('key')
                            ->label('Key (optional)')
                            ->helperText('Optional identifier to reference this block in templates')
                            ->placeholder('e.g. hero-title'),
                    ])->columnSpanFull(),

                    Tabs::make('section_tabs')->tabs([
                        Tab::make('Title')
                            ->schema([
                                TextInput::make('title')
                                    ->label('Title')
                                    ->maxLength(160),
                            ])
                            ->visible(fn ($get) => $get('type') === 'title'),

                        Tab::make('Paragraph')
                            ->schema([
                                Textarea::make('text')
                                    ->label('Text')
                                    ->rows(4),
                            ])
                            ->visible(fn ($get) => $get('type') === 'paragraph'),

                        Tab::make('Media')
                            ->schema([
                                FileUpload::make('image')
                                    ->image()
                                    ->disk('public')
                                    ->directory(fn () => 'triplinks/'.(optional(Auth::user()?->current_team)->id ?? 'unknown')),

                                Grid::make()->schema([
                                    Select::make('image_shape')
                                        ->label('Shape')
                                        ->options([
                                            'rect' => 'Rectangle',
                                            'circle' => 'Circle',
                                        ])
                                        ->default('rect'),

                                    TextInput::make('image_width')
                                        ->label('Width (px or %)'),

                                    TextInput::make('image_height')
                                        ->label('Height (px or %)'),

                                    TextInput::make('image_radius')
                                        ->label('Radius (px)')
                                        ->numeric()
                                        ->default(0),
                                ])->columns(2),

                                FileUpload::make('gallery_images')
                                    ->label('Gallery images')
                                    ->image()
                                    ->multiple()
                                    ->maxFiles(9)
                                    ->disk('public')
                                    ->directory(fn () => 'triplinks/'.(optional(Auth::user()?->current_team)->id ?? 'unknown')),

                                TextInput::make('gallery_columns')
                                    ->label('Gallery columns')
                                    ->numeric()
                                    ->default(3),
                            ])
                            ->visible(fn ($get) => in_array($get('type'), ['image', 'gallery'], true)),

                        Tab::make('Button')
                            ->schema([
                                Tabs::make('button_tabs')->tabs([
                                    Tab::make('Content')
                                        ->schema([
                                            Grid::make()->schema([
                                                TextInput::make('button_label')
                                                    ->label('Label')
                                                    ->maxLength(120),

                                                TextInput::make('button_url')
                                                    ->label('URL')
                                                    ->url()
                                                    ->maxLength(2048),
                                            ])->columns(2),

                                            Radio::make('button_bg_mode')
                                                ->label('Background')
                                                ->options([
                                                    'color' => 'Color',
                                                    'image' => 'Image',
                                                ])
                                                ->default('color')
                                                ->reactive(),

                                            ColorPicker::make('button_color')
                                                ->label('Button color')
                                                ->default('#f87171')
                                                ->visible(fn ($get) => ($get('button_bg_mode') ?? 'color') === 'color'),

                                            FileUpload::make('button_bg_image')
                                                ->label('Background image')
                                                ->image()
                                                ->disk('public')
                                                ->directory(fn () => 'triplinks/'.(optional(Auth::user()?->current_team)->id ?? 'unknown'))
                                                ->visible(fn ($get) => ($get('button_bg_mode') ?? 'color') === 'image'),

                                            Grid::make()->schema([
                                                TextInput::make('button_bg_width')
                                                    ->label('Width (px)')
                                                    ->numeric()
                                                    ->placeholder('e.g. 280')
                                                    ->visible(fn ($get) => ($get('button_bg_mode') ?? 'color') === 'image'),

                                                TextInput::make('button_bg_height')
                                                    ->label('Height (px)')
                                                    ->numeric()
                                                    ->placeholder('e.g. 48')
                                                    ->visible(fn ($get) => ($get('button_bg_mode') ?? 'color') === 'image'),
                                            ])->columns(2),
                                        ]),

                                    // Border tab removed — border settings are handled in the shared Style tab
                                ])->columnSpanFull(),
                            ])
                            ->visible(fn ($get) => $get('type') === 'button'),

                        Tab::make('Style')
                            ->schema([
                                Grid::make()->schema([
                                    Select::make('alignment')
                                        ->label('Alignment')
                                        ->options([
                                            'left' => 'Left',
                                            'center' => 'Center',
                                            'right' => 'Right',
                                        ])
                                        ->default('center'),
                                ])->columns(2),

                                // Button-specific style controls — grouped for clarity.
                                Grid::make()->schema([
                                    // Size: width and height
                                    TextInput::make('button_width')
                                        ->label('Button width (px)')
                                        ->numeric()
                                        ->placeholder('e.g. 280')
                                        ->helperText('Fixed width for the button (in px). Leave blank for auto width.')
                                        ->visible(fn ($get) => $get('type') === 'button'),

                                    TextInput::make('button_height')
                                        ->label('Button height (px)')
                                        ->numeric()
                                        ->placeholder('e.g. 48')
                                        ->helperText('Fixed height for the button (in px). Sets height and line-height.')
                                        ->visible(fn ($get) => $get('type') === 'button'),

                                    // Shape: radius
                                    TextInput::make('button_radius')
                                        ->label('Radius (px)')
                                        ->numeric()
                                        ->default(12)
                                        ->visible(fn ($get) => $get('type') === 'button'),

                                    // Border: width, style, color
                                    TextInput::make('button_border_width')
                                        ->label('Border width (px)')
                                        ->numeric()
                                        ->default(1)
                                        ->visible(fn ($get) => $get('type') === 'button'),

                                    Select::make('button_border_style')
                                        ->label('Border style')
                                        ->options([
                                            'solid' => 'Solid',
                                            'dashed' => 'Dashed',
                                            'dotted' => 'Dotted',
                                            'double' => 'Double',
                                        ])
                                        ->default('solid')
                                        ->visible(fn ($get) => $get('type') === 'button'),

                                    ColorPicker::make('button_border_color')
                                        ->label('Border color')
                                        ->default('#000000')
                                        ->visible(fn ($get) => $get('type') === 'button'),

                                    // Text color
                                    ColorPicker::make('button_text_color')
                                        ->label('Button text color')
                                        ->default('#ffffff')
                                        ->visible(fn ($get) => $get('type') === 'button'),
                                ])->columns(2),

                                // Per-button padding and margin editor (top/right/bottom/left)
                                TextInput::make('button_padding')
                                    ->label('Button padding (t r b l)')
                                    ->helperText('Enter four numbers separated by spaces or commas (top right bottom left).')
                                    ->placeholder('e.g. 0 12 0 12')
                                    ->visible(fn ($get) => $get('type') === 'button')
                                    ->columnSpanFull(),

                                TextInput::make('button_margin')
                                    ->label('Button margin (t r b l)')
                                    ->helperText('Enter four numbers separated by spaces or commas (top right bottom left).')
                                    ->placeholder('e.g. 0 0 12 0')
                                    ->visible(fn ($get) => $get('type') === 'button')
                                    ->columnSpanFull(),
                            ])
                            ->visible(fn ($get) => true),
                    ])->columnSpanFull(),

                ])
                ->columnSpanFull(),
        ];
    }

    public function submit(): void
    {
        $teamId = optional(Auth::user()?->current_team)->id ?? null;
        $trip = TripLinkModel::firstOrNew(['team_id' => $teamId ?? 0]);

        $state = $this->form->getState();

        // Normalize any padding/margin string inputs into an array shape that
        // the public renderer expects: ['top'=>int,'right'=>int,'bottom'=>int,'left'=>int]
        $layout = $state['layout'] ?? [];

        $parseEdge = function ($value) {
            // If already an array with keys, return as-is (cast values to int).
            if (is_array($value)) {
                $keys = ['top', 'right', 'bottom', 'left'];
                $out = [];
                foreach ($keys as $k) {
                    $out[$k] = isset($value[$k]) ? (int) $value[$k] : 0;
                }

                return $out;
            }

            if (! is_string($value) && ! is_numeric($value)) {
                return ['top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0];
            }

            // Split by commas or whitespace, filter empty, parse ints
            $parts = preg_split('/[\s,]+/', trim((string) $value));
            $nums = array_values(array_filter(array_map(function ($p) {
                if ($p === '') {
                    return null;
                }

                return is_numeric($p) ? (int) $p : null;
            }, $parts), function ($v) {
                return $v !== null;
            }));

            // Interpret like CSS shorthand: 1,2,3,4 values
            $count = count($nums);
            if ($count === 0) {
                return ['top' => 0, 'right' => 0, 'bottom' => 0, 'left' => 0];
            }
            if ($count === 1) {
                return ['top' => $nums[0], 'right' => $nums[0], 'bottom' => $nums[0], 'left' => $nums[0]];
            }
            if ($count === 2) {
                return ['top' => $nums[0], 'right' => $nums[1], 'bottom' => $nums[0], 'left' => $nums[1]];
            }
            if ($count === 3) {
                return ['top' => $nums[0], 'right' => $nums[1], 'bottom' => $nums[2], 'left' => $nums[1]];
            }

            // 4 or more
            return ['top' => $nums[0], 'right' => $nums[1], 'bottom' => $nums[2], 'left' => $nums[3]];
        };

        foreach ($layout as $i => $item) {
            if (! is_array($item)) {
                continue;
            }

            if (isset($item['button_padding'])) {
                $layout[$i]['button_padding'] = $parseEdge($item['button_padding']);
            }
            if (isset($item['button_margin'])) {
                $layout[$i]['button_margin'] = $parseEdge($item['button_margin']);
            }
        }

        $trip->layout = $layout;

        $trip->save();

        Notification::make()->title('Layout saved')->success()->send();
        $this->redirect(static::getUrl(), navigate: true);
    }

    protected function getFormStatePath(): ?string
    {
        return 'data';
    }
}
