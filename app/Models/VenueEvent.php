<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VenueEvent extends Model
{
    protected $table = 'venue_events';

    protected $fillable = [
        'venue_id',
        'name',
        'description',
        'starts_at',
        'ends_at',
        'team_id',
    ];
}
