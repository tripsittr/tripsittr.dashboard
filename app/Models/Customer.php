<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Filament\Facades\Filament;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'team_id','name','email','phone','company','address_line1','address_line2','city','region','postal_code','country','notes'
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    protected static function booted(): void
    {
        static::creating(function (Customer $customer) {
            if (empty($customer->team_id) && Filament::getTenant()) {
                $customer->team_id = Filament::getTenant()->id;
            }
        });

        static::created(function (Customer $customer) {
            \App\Services\LogActivity::record('customer.created', 'Customer', $customer->id, $customer->only($customer->getFillable()));
        });

        static::updating(function (Customer $customer) {
            $diff = [];
            foreach ($customer->getDirty() as $field => $new) {
                if (! in_array($field, $customer->getFillable(), true)) {
                    continue;
                }
                $original = $customer->getOriginal($field);
                if ($original !== $new) {
                    $diff[$field] = ['old' => $original, 'new' => (string)$new];
                }
            }
            if ($diff) {
                \App\Services\LogActivity::record('customer.updated', 'Customer', $customer->id, $diff);
            }
        });

        static::deleted(function (Customer $customer) {
            \App\Services\LogActivity::record('customer.deleted', 'Customer', $customer->id, []);
        });
    }
}
