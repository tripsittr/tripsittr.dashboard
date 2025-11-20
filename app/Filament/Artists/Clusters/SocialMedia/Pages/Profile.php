<?php

namespace App\Filament\Artists\Clusters\SocialMedia\Pages;

use Filament\Pages\Page;

class Profile extends Page
{
    protected static string $view = 'filament.clusters.socialmedia.pages.profile';
    protected static ?string $cluster = \App\Filament\Artists\Clusters\SocialMedia\SocialMedia::class;
    protected static ?string $navigationIcon = 'heroicon-s-user-group';
    protected static ?string $navigationLabel = 'Profile';
    protected static ?string $slug = 'social-media/profile';

    public ?string $provider = null;

    public function mount(): void
    {
        $this->provider = request('provider', 'facebook');
    }
}
