<?php

namespace App\Models;
use App\Models\Team;
use App\Models\User;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    protected $fillable = [
        'team_id','entity_type','entity_id','action','changes','user_id'
    ];

    protected $casts = [
        'changes' => 'array',
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
