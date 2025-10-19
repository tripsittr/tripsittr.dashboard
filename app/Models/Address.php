<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Address extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'team_id','label','line1','line2','city','region','postal_code','country','hash'
    ];

    protected static function booted(): void
    {
        static::creating(function(Address $address){
            $address->hash = $address->hash ?? substr(sha1(strtolower(trim($address->line1.'|'.$address->postal_code.'|'.$address->country))),0,32);
        });
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
