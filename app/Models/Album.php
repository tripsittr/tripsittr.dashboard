<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use App\Traits\BlacklistedWordsTrait;

class Album extends Model {
    protected $fillable = ['title', 'release_date', 'band_id', 'artist_id', 'team_id']; // Ensure 'team_id' is included

    use BlacklistedWordsTrait;

    protected static function boot() {
        parent::boot();

        static::creating(function ($album) {
            if (Auth::check()) {
                $user = Auth::user();
                $tenant = $user->currentTeam; // Assuming tenancy is tied to teams

                if ($tenant) {
                    $album->team_id = $tenant->id; // Ensure 'team_id' matches the database column
                }
            }
        });
    }

    public function songs() {
        return $this->hasMany(Song::class);
    }

    public function team(): BelongsTo {
        return $this->belongsTo(Team::class);
    }

    public function scopeForTenant($query, $tenantId) {
        return $query->where('team_id', $tenantId); // Ensure 'team_id' matches the database column
    }

    public function getBlacklistedFields(): array {
        return array_merge($this->fillable, ['name', 'description']);
    }
}
