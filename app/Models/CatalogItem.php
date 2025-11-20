<?php

namespace App\Models;
use App\Models\Team;
use App\Models\InventoryItem;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Filament\Facades\Filament;

class CatalogItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'team_id','part_number','reference_code','name','description','material','brand','default_cost','default_price','length','width','height','dims_unit','weight','weight_unit','default_lead_time_days','notes',
        'item_type','sizes','colors','format','runtime_minutes','warranty_months'
    ];

    protected $casts = [
        'sizes' => 'array',
        'colors' => 'array',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function inventoryItems(): HasMany
    {
        return $this->hasMany(InventoryItem::class);
    }

    protected static function booted(): void
    {
        static::creating(function (CatalogItem $item) {
            if (empty($item->team_id) && Filament::getTenant()) {
                $item->team_id = Filament::getTenant()->id;
            }
        });

        static::created(function (CatalogItem $item) {
            \App\Services\LogActivity::record('catalog.created', 'CatalogItem', $item->id, $item->only($item->getFillable()));
        });

        static::updating(function (CatalogItem $item) {
            $diff = [];
            foreach ($item->getDirty() as $field => $new) {
                // Only log fillable fields to avoid noise
                if (! in_array($field, $item->getFillable(), true)) {
                    continue;
                }
                $original = $item->getOriginal($field);
                if ($original !== $new) {
                    $diff[$field] = ['old' => $original, 'new' => (string)$new];
                }
            }
            if ($diff) {
                \App\Services\LogActivity::record('catalog.updated', 'CatalogItem', $item->id, $diff);
            }
        });

        static::deleted(function (CatalogItem $item) {
            \App\Services\LogActivity::record('catalog.deleted', 'CatalogItem', $item->id, []);
        });
    }
}
