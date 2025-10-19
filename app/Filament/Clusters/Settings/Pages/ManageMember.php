<?php

namespace App\Filament\Clusters\Settings\Pages;

use App\Filament\Clusters\Settings\Settings;
use App\Models\User;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\PermissionRegistrar;

class ManageMember extends Page
{
    protected static ?string $cluster = Settings::class;

    protected static ?string $navigationIcon = null;

    protected static ?string $navigationLabel = 'Manage Member';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $slug = 'team-members/{member}/manage';

    protected static string $view = 'filament.settings.team.manage-member';

    public ?User $member = null;

    public function mount(User $member): void
    {
        $this->member = $member;
    $team = Auth::user()?->current_team;
        abort_unless($team && $this->member->teams()->where('teams.id', $team->id)->exists(), 403);
        app(PermissionRegistrar::class)->setPermissionsTeamId($team->id);
    }

    public function getTitle(): string
    {
        return 'Manage Member';
    }
}
