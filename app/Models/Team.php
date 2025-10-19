<?php

namespace App\Models;

use App\Traits\BlacklistedWordsTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Laravel\Cashier\Billable;
use App\Models\CatalogItem;
use App\Models\InventoryItem;
use App\Models\Customer;
use App\Models\Order;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;

class Team extends Model
{
    use Billable, BlacklistedWordsTrait, HasFactory, SoftDeletes;

    protected $fillable = ['name', 'type', 'members', 'team_avatar', 'formation_date', 'genre', 'website', 'instagram', 'twitter', 'facebook', 'youtube', 'email', 'phone'];

    // mediaUploads relation removed along with custom media system

    public function songs(): HasMany
    {
        return $this->hasMany(Song::class);
    }

    public function albums(): HasMany
    {
        return $this->hasMany(Album::class);
    }

    public function inventoryItems(): HasMany
    {
        return $this->hasMany(InventoryItem::class);
    }

    public function catalogItems(): HasMany
    {
        return $this->hasMany(CatalogItem::class);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(Invitation::class);
    }

    public function userActions(): HasMany
    {
        return $this->hasMany(UserAction::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'team_user');
    }

    protected static function booted()
    {
        static::created(function (Team $team) {
            // Attach creating user as Admin (first member gets full permissions)
            $creator = Auth::user();
            if ($creator) {
                $team->users()->syncWithoutDetaching([$creator->id]);
                // team-scoped role assignment
                app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($team->id);
                $role = \Spatie\Permission\Models\Role::firstOrCreate([
                    'name' => 'Admin',
                    'team_id' => $team->id,
                    'guard_name' => 'web',
                ]);
                if (! $creator->roles()->where('roles.name', 'Admin')->where('roles.team_id', $team->id)->exists()) {
                    $creator->assignRole($role->name);
                }
            }
        });
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class, 'team_id');
    }

    public function scopeForTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function getBlacklistedFields(): array
    {
        return array_merge($this->fillable, ['name', 'description']);
    }

    public function planConfig(): ?array
    {
        $plans = config('plans.plans');
        return $plans[$this->plan_slug] ?? ($plans[config('plans.default_plan')] ?? null);
    }

    public function maxSeats(): int
    {
        return (int)($this->planConfig()['seats'] ?? 1);
    }

    public function usedSeats(): int
    {
        // Count distinct user ids to avoid inflated seat usage if duplicate pivot rows exist
        return (int) $this->users()->distinct('users.id')->count('users.id');
    }

    /**
     * Remove accidental duplicate pivot rows (same team_id,user_id) and return number removed.
     */
    public function purgeDuplicateMembers(): int
    {
        $duplicates = DB::table('team_user')
            ->select('user_id', DB::raw('COUNT(*) as c'))
            ->where('team_id', $this->id)
            ->groupBy('user_id')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        $removed = 0;
        foreach ($duplicates as $dup) {
            $toDelete = DB::table('team_user')
                ->where('team_id', $this->id)
                ->where('user_id', $dup->user_id)
                ->orderBy('id')
                ->skip(1) // keep the first
                ->take(PHP_INT_MAX)
                ->pluck('id');
            if ($toDelete->isNotEmpty()) {
                $removed += DB::table('team_user')->whereIn('id', $toDelete)->delete();
            }
        }
        return $removed;
    }

    public function hasSeatAvailable(): bool
    {
        return $this->usedSeats() < $this->maxSeats();
    }
}
