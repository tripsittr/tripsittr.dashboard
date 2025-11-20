<?php

namespace App\Filament\Artists\Clusters\SocialMedia\Pages;

use Filament\Pages\Page;

class Analytics extends Page
{
    protected static string $view = 'filament.clusters.socialmedia.pages.analytics';
    protected static ?string $cluster = \App\Filament\Artists\Clusters\SocialMedia\SocialMedia::class;
    protected static ?string $navigationIcon = 'heroicon-s-chart-pie';
    protected static ?string $navigationLabel = 'Analytics';
    protected static ?string $slug = 'social-media/analytics';
}
