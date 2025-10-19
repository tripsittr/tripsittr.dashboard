<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Collection;

class Admin
{
    public static function users(): Collection
    {
        return User::query()->where('type', 'Admin')->get();
    }
}
