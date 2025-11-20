<?php

namespace App\Filament\Artists\Clusters\Settings\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class SocialConnections extends Page
{
    protected static string $view = 'filament.settings.social-connections';
    protected static ?string $cluster = \App\Filament\Artists\Clusters\Settings\Settings::class;
    protected static ?string $navigationIcon = 'heroicon-s-share';
    protected static ?string $navigationLabel = 'Social Connections';

    public array $connected = [];
    public array $connectedAccounts = [];

    public function mount(): void
    {
        $accounts = Auth::user()->socialAccounts()->get();
        $this->connected = $accounts->pluck('provider')->map(fn($p) => (string)$p)->all();
        // Map provider => id for quick lookup in the view
        $this->connectedAccounts = $accounts->mapWithKeys(fn($a) => [$a->provider => $a->id])->all();
    }

    protected function getHeaderWidgets(): array
    {
        return [];
    }
}
