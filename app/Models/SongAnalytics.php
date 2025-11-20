<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SongAnalytics extends Model
{
    protected $table = 'song_analytics';

    protected $guarded = [];

    protected $casts = [
        'streams' => 'integer',
        'streams_pct' => 'float',
        'streams_change' => 'integer',
        'streams_change_pct' => 'float',
        'downloads' => 'integer',
        'downloads_pct' => 'float',
        'downloads_change' => 'integer',
        'downloads_change_pct' => 'float',
        'imported_at' => 'datetime',
    ];
}
