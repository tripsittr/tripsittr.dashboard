<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Event extends Model {
    use HasFactory;

    public function team(): BelongsTo {
        return $this->belongsTo(Team::class);
    }

    protected $fillable = [
        'name',
        'description',
        'starts_at',
        'ends_at',
        'team_id',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];


    public function scopeForTenant($query, $tenantId) {

        return $query->where('team_id', $tenantId);
    }
}
