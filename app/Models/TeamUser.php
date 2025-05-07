<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamUser extends Model
{
    
    protected $table = 'team_user';
    protected $fillable = [
        'user_id',
        'team_id',
    ];
}
