<?php
namespace App\Models;

use App\Filament\Index\Traits\BlacklistedWordsTrait;
use Filament\Facades\Filament;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\HasTenants;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Laravel\Cashier\Billable;
use Spatie\Permission\Traits\HasRoles;


class User extends Authenticatable implements FilamentUser, HasAvatar, HasTenants
{
    use HasFactory, Notifiable, SoftDeletes;
    use HasRoles; // Enable Cashier subscription & billing methods

    public function getDefaultTenant(Panel $panel): ?Model
    {
        return $this->latestTeam;
    }

    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('team_id', $tenantId);
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class);
    }

    public function userActions(): HasMany
    {
        return $this->hasMany(UserAction::class);
    }

    public function getTenants(Panel $panel): Collection
    {
        return $this->teams;
    }

    public function canAccessTenant(Model $tenant): bool
    {
        // Allow global access for platform admins
        if ($this->type === 'Admin' || $this->hasRole('Admin')) {
            return true;
        }

        return $this->teams()->whereKey($tenant)->exists();
    }

    public function isTeamAdmin(Team $team): bool
    {
        return $this->hasRole('Admin') && $this->teams()->where('teams.id', $team->id)->exists();
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'instagram',
        'twitter',
        'facebook',
        'linkedin',
        'website',
        'type',
        'team_id',
        'instagram_access_token',
        'instagram_user_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getBlacklistedFields(): array
    {
        return array_merge($this->fillable, ['name', 'description']);
    }

    /**
     * Resolve the user's current team (active tenant) for convenience.
     * Falls back to first associated team.
     */
    public function getCurrentTeamAttribute(): ?Team
    {
        // If Filament has a current tenant selected, prefer that.
        try {
            $tenant = \Filament\Facades\Filament::getTenant();
            if ($tenant instanceof Team && $this->teams->contains('id', $tenant->id)) {
                return $tenant;
            }
        } catch (\Throwable $e) {
            // ignore if Filament not resolved yet
        }
        // Eager loaded relationship check
        if ($this->relationLoaded('teams') && $this->teams->isNotEmpty()) {
            return $this->teams->first();
        }

        // Query for first team membership
        return $this->teams()->first();
    }

    public function socialAccounts(): HasMany
    {
        return $this->hasMany(SocialAccount::class);
    }

    // Backwards compatibility helper (method style)
    public function currentTeam(): ?Team
    {
        return $this->current_team; // will invoke accessor
    }
}
