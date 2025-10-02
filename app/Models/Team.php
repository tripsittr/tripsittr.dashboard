<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Cashier\Billable;
use App\Traits\BlacklistedWordsTrait;

class Team extends Model
{
    use HasFactory, Billable, BlacklistedWordsTrait;

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

    public function InventoryItems(): HasMany
    {
        return $this->hasMany(InventoryItem::class);
    }

    public function userActions(): HasMany
    {
        return $this->hasMany(UserAction::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'team_user');
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
}
