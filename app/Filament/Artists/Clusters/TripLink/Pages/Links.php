<?php

namespace App\Filament\Artists\Clusters\TripLink\Pages;

use App\Filament\Artists\Clusters\TripLink\TripLink as TripLinkCluster;
use App\Models\TripLink as TripLinkModel;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Links extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $cluster = TripLinkCluster::class;

    protected static ?string $navigationLabel = 'Links';

    protected static ?string $navigationIcon = 'heroicon-s-link';

    protected static string $view = 'filament.clusters.triplink.pages.links';

    public static function shouldRegisterNavigation(): bool
    {
        // Hide the legacy Links page from navigation once merged into Content
        return false;
    }

    public array $data = [];

    public function mount(): void
    {
        $teamId = optional(Auth::user()?->current_team)->id ?? null;
        // Avoid creating the DB record during mount(): fetch if exists or use an
        // unsaved model instance so viewing the page doesn't attempt an insert
        // that would fail because `slug` is required.
        $trip = TripLinkModel::where('team_id', $teamId ?? 0)->first();
        if (! $trip) {
            $trip = new TripLinkModel;
            $trip->team_id = $teamId ?? 0;
        }

        $this->form->fill([
            'links' => $trip->links ?? [],
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            Repeater::make('links')
                ->schema([
                    TextInput::make('label')->required()->maxLength(80),
                    TextInput::make('url')->required()->url()->maxLength(2048),
                    Select::make('target')->options(['_self' => 'Same tab', '_blank' => 'New tab'])->default('_blank'),
                    Select::make('icon')
                        ->label('Icon')
                        ->options([
                            '' => 'None',
                            'heroicon-s-link' => 'Link',
                            'heroicon-s-user' => 'User',
                            'heroicon-s-music-note' => 'Music',
                            'heroicon-s-camera' => 'Camera',
                            'heroicon-s-heart' => 'Heart',
                            'heroicon-s-globe-alt' => 'Website',
                        ])
                        ->helperText('Choose a small icon for this link (curated set).'),
                    Toggle::make('accent')->label('Accent')->helperText('Make this link use the primary accent color on the public page')->default(false),
                ])
                ->columnSpanFull()
                ->reorderable()
                ->createItemButtonLabel('Add Link'),
        ];
    }

    public function submit(): void
    {
        $teamId = optional(Auth::user()?->current_team)->id ?? null;
        $trip = TripLinkModel::firstOrNew(['team_id' => $teamId ?? 0]);

        // Ensure slug exists for newly created TripLink records
        if (empty($trip->slug)) {
            $teamName = optional(Auth::user()?->current_team)->name ?? null;
            $base = Str::slug($trip->title ?? $teamName ?? ('team-'.($teamId ?? '0')));
            $candidate = $base ?: 'team-'.($teamId ?? '0');
            $i = 1;
            while (TripLinkModel::where('slug', $candidate)->where('id', '<>', $trip->id)->exists()) {
                $candidate = $base.'-'.$i++;
            }
            $trip->slug = $candidate;
        }

        $state = $this->form->getState();
        $trip->links = $state['links'] ?? [];
        $trip->save();

        Notification::make()->title('Links saved')->success()->send();
        $this->redirect(static::getUrl(), navigate: true);
    }

    protected function getFormStatePath(): ?string
    {
        return 'data';
    }
}
