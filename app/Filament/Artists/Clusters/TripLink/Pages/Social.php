<?php

namespace App\Filament\Artists\Clusters\TripLink\Pages;

use App\Filament\Artists\Clusters\TripLink\TripLink as TripLinkCluster;
use App\Models\TripLink as TripLinkModel;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Social extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $cluster = TripLinkCluster::class;

    protected static ?string $navigationLabel = 'Social';

    protected static ?string $navigationIcon = 'heroicon-s-hashtag';

    protected static string $view = 'filament.clusters.triplink.pages.social';

    public static function shouldRegisterNavigation(): bool
    {
        // Hide the legacy Social page from navigation once merged into Content
        return false;
    }

    public array $data = [];

    public function mount(): void
    {
        $teamId = optional(Auth::user()?->current_team)->id ?? null;
        // Avoid creating DB rows during mount; prepare an unsaved model if none exists.
        $trip = TripLinkModel::where('team_id', $teamId ?? 0)->first();
        if (! $trip) {
            $trip = new TripLinkModel;
            $trip->team_id = $teamId ?? 0;
        }

        $this->form->fill([
            'social' => $trip->social ?? [],
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            Repeater::make('social')
                ->schema([
                    TextInput::make('platform')->required()->maxLength(80),
                    TextInput::make('url')->required()->url()->maxLength(2048),
                    TextInput::make('handle')->maxLength(80)->helperText('Optional handle or label'),
                    Select::make('icon')
                        ->label('Icon')
                        ->options([
                            '' => 'None',
                            'heroicon-s-hashtag' => 'Hashtag',
                            'heroicon-s-user' => 'User',
                            'heroicon-s-globe-alt' => 'Website',
                            'heroicon-s-mail' => 'Email',
                            'heroicon-s-phone' => 'Phone',
                        ])
                        ->helperText('Choose an icon for this social link (curated set).'),
                ])
                ->columnSpanFull()
                ->reorderable()
                ->createItemButtonLabel('Add Social Link'),
        ];
    }

    public function submit(): void
    {
        $teamId = optional(Auth::user()?->current_team)->id ?? null;
        $trip = TripLinkModel::firstOrNew(['team_id' => $teamId ?? 0]);

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
        $trip->social = $state['social'] ?? [];
        $trip->save();

        Notification::make()->title('Social links saved')->success()->send();
        $this->redirect(static::getUrl(), navigate: true);
    }

    protected function getFormStatePath(): ?string
    {
        return 'data';
    }
}
