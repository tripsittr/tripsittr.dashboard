<?php

namespace App\Filament\Artists\Clusters\SocialMedia\Pages;

use Filament\Pages\Page;

class Scheduler extends Page
{
    protected static string $view = 'filament.clusters.socialmedia.pages.scheduler';
    protected static ?string $cluster = \App\Filament\Artists\Clusters\SocialMedia\SocialMedia::class;
    protected static ?string $navigationIcon = 'heroicon-s-clock';
    protected static ?string $navigationLabel = 'Scheduler';
    protected static ?string $slug = 'social-media/scheduler';
}
