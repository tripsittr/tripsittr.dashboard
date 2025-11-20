<?php
namespace App\Filament\Admin\Clusters\Admin;

use Filament\Clusters\Cluster;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Auth;

class Admin extends Cluster
{
    protected static ?string $navigationIcon = 'fas-lock';
    protected static bool $shouldRegisterNavigation = false; // keep nav hidden unless needed

    public static function canAccess(): bool
    {
        // Return true when unauthenticated so routes still register; per-page/resource auth will handle access.
        $user = Auth::user();
        if (! $user) {
            return true;
        }
        return ($user->type === 'Admin') || $user->hasRole('Admin');
    }

    public static function getSlug(): string
    {
        return 'admin';
    }
}
