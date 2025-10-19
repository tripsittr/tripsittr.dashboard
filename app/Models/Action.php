<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Action extends Model
{
    protected $fillable = [ 'action_title', 'action_type' ];

    protected $casts = [ 'created_at' => 'datetime', 'updated_at' => 'datetime' ];
    // Table now plural (normalization migration handles rename if legacy existed)
    protected $table = 'actions';
}
