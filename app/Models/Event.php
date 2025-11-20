<?php

namespace App\Models;
use App\Models\Team;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Event extends Model
{
    use HasFactory;

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    // id, name, description, starts_at, ends_at, team_id, created_at, updated_at, venue, priority, notes, status, contact_name, contact_email, contact_phone, contact_website, contact_address, contact_address2, contact_city, contact_state, contact_zip, contact_country, author_id
    protected $fillable = [
        'name',
        'description',
        'priority',
        'venue',
        'type',
        'status',
        'contact_name',
        'contact_email',
        'contact_phone',
        'contact_website',
        'contact_address',
        'contact_address2',
        'contact_city',
        'contact_state',
        'contact_zip',
        'contact_country',
        'author_id',
        'notes',
        'starts_at',
        'ends_at',
        'team_id',
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];


    public function scopeForTenant($query, $tenantId)
    {

        if (Filament::getTenant()->type == 'Admin') {
            return;
        }

        return $query->where('team_id', $tenantId);
    }
}
