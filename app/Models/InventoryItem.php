<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\BlacklistedWordsTrait;

class InventoryItem extends Model
{
    use HasFactory;
    use BlacklistedWordsTrait;
    use SoftDeletes;

    protected $table = 'inventory_items';

    // Removed duplicate descriptive & dimensional fields that exist on CatalogItem.
    // Keep only variant / operational attributes here.
    protected $fillable = [
        'sku', 'batch_number', 'serial_number', 'barcode',
        'size', 'color', // variant-specific differentiators
        'model', 'provider', 'provider_website', // optional sourcing metadata
        'stock', 'reserved', 'location', 'status', 'catalog_item_id',
        'override_price','price_override','override_cost','cost_override',
        'low_stock_threshold', 'image', 'band_id', 'user_id', 'owner', 'team_id', 'tenant_id', // keep tenant_id for legacy rows
    ];

    protected $casts = [
        // retained for potential per-item overrides if columns still exist in DB
        'price' => 'decimal:2',
        'cost' => 'decimal:2',
        'exp_date' => 'date',
        'length' => 'decimal:2',
        'width' => 'decimal:2',
        'height' => 'decimal:2',
        'weight' => 'decimal:2',
        'stock' => 'integer',
        'reserved' => 'integer',
        'low_stock_threshold' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();
        // Ensure team_id (primary tenancy) is set on creation; keep backward compat with tenant_id if present
        static::creating(function (InventoryItem $item) {
            $tenant = Filament::getTenant();
            if ($tenant) {
                if (empty($item->team_id)) {
                    $item->team_id = $tenant->id;
                }
                // If legacy code set tenant_id but not team_id, sync them
                if (!empty($item->tenant_id) && empty($item->team_id)) {
                    $item->team_id = $item->tenant_id;
                }
                if (empty($item->tenant_id)) {
                    $item->tenant_id = $item->team_id; // keep both populated for now
                }
            }
        });
        static::updating(function (InventoryItem $item) {
            $tenant = Filament::getTenant();
            if ($tenant && empty($item->team_id)) {
                $item->team_id = $tenant->id;
            }
            if (!empty($item->tenant_id) && empty($item->team_id)) {
                $item->team_id = $item->tenant_id;
            }
            if (empty($item->tenant_id) && !empty($item->team_id)) {
                $item->tenant_id = $item->team_id;
            }
        });
        static::updated(function(InventoryItem $item){
            \App\Services\LogActivity::record('inventory.updated','InventoryItem',$item->id,$item->getChanges(), $item->team_id);
            if (empty($item->barcode) && $item->sku) {
                $item->barcode = strtoupper(preg_replace('/[^A-Z0-9]/i','', $item->sku . '-' . ($item->batch_number ?? 'GEN'))) . '-' . substr(md5(uniqid('', true)),0,6);
            }
        });
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function catalogItem(): BelongsTo
    {
        return $this->belongsTo(CatalogItem::class);
    }

    /*
     |--------------------------------------------------------------------------
     | Fallback Accessors
     |--------------------------------------------------------------------------
     | If a legacy duplicate column is null (or was removed from forms),
     | transparently fallback to the related CatalogItem's canonical value.
     */
    public function getNameAttribute($value)
    { return $value ?? $this->catalogItem?->name; }

    public function getDescriptionAttribute($value)
    { return $value ?? $this->catalogItem?->description; }

    public function getMaterialAttribute($value)
    { return $value ?? $this->catalogItem?->material; }

    public function getBrandAttribute($value)
    { return $value ?? $this->catalogItem?->brand; }

    public function getPriceAttribute($value)
    {
        if ($this->override_price && ! is_null($this->price_override)) {
            return $this->price_override;
        }
        return $this->catalogItem?->default_price;
    }

    public function getCostAttribute($value)
    {
        if ($this->override_cost && ! is_null($this->cost_override)) {
            return $this->cost_override;
        }
        return $this->catalogItem?->default_cost;
    }

    public function getLengthAttribute($value)
    { return $value ?? $this->catalogItem?->length; }

    public function getWidthAttribute($value)
    { return $value ?? $this->catalogItem?->width; }

    public function getHeightAttribute($value)
    { return $value ?? $this->catalogItem?->height; }

    public function getDimsUnitAttribute($value)
    { return $value ?? $this->catalogItem?->dims_unit; }

    public function getWeightAttribute($value)
    { return $value ?? $this->catalogItem?->weight; }

    public function getWeightUnitAttribute($value)
    { return $value ?? $this->catalogItem?->weight_unit; }

    public function getOwnerAttribute(): string
    {
        return '(Band) ' . $this->band?->name ?? '(User) ' . $this->user?->name ?? 'N/A';
    }

    // Accessors for formatted attributes
    public function getFormattedPriceAttribute()
    {
        return $this->price ? '$' . number_format($this->price, 2) : 'N/A';
    }

    public function getFormattedWeightAttribute()
    {
        return $this->weight ? "{$this->weight} {$this->weight_unit}" : 'N/A';
    }

    public function getFormattedDimensionsAttribute()
    {
        if (!$this->length || !$this->width || !$this->height) return 'N/A';
        return "{$this->length} × {$this->width} × {$this->height} {$this->dims_unit}";
    }

    public function scopeForTenant($query, $tenantId)
    {
        return $query->where(function($q) use ($tenantId) {
            $q->where('team_id', $tenantId)->orWhere('tenant_id', $tenantId);
        });
    }

    public function getBlacklistedFields(): array
    {
        return array_merge($this->fillable, ['name', 'description']);
    }

    // Always eager load catalog item to avoid N+1 in tables
    protected $with = ['catalogItem'];

    public function getVariantLabelAttribute(): string
    {
        $base = $this->catalogItem?->name ?? 'Item';
        $type = $this->catalogItem?->item_type;
        if ($type === 'clothing') {
            $parts = [$base];
            if ($this->size) $parts[] = '[' . $this->size . ']';
            if ($this->color) $parts[] = $this->color;
            return trim(implode(' ', $parts));
        }
        if ($this->color) return $base . ' ' . $this->color;
        return $base;
    }
}
