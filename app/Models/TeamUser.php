<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TeamUser extends Model
{
    use SoftDeletes;
    
    protected $table = 'team_user';
    protected $fillable = [
        'user_id',
        'team_id',
    ];
}
