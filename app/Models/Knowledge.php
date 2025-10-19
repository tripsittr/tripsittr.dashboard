<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Knowledge extends Model {
    use SoftDeletes;

    protected $table = 'knowledge';

    protected $fillable = [
        'title',
        'slug',
        'description',
        'content',
        'image',
        'type',
        'status',
        'author',
        'source',
        'source_url',
    ];

    public function getRouteKeyName() {
        return 'slug';
    }

    public function scopeVisibleToUser($query) {
        return $query->when(Auth::check() && !Auth::user()->is_admin, function ($q) {
            $q->where('status', '!=', 'draft');
        });
    }
}
