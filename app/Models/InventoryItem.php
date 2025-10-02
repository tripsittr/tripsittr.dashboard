<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\BlacklistedWordsTrait;

class InventoryItem extends Model
{
    use HasFactory;
    use BlacklistedWordsTrait;

    protected $table = 'inventory_items';

    protected $fillable = [
        'sku',
        'batch_number',
        'barcode',
        'name',
        'description',
        'price',
        'cost',
        'exp_date',
        'length',
        'width',
        'height',
        'dims_unit',
        'weight',
        'weight_unit',
        'size',
        'color',
        'material',
        'brand',
        'model',
        'provider',
        'provider_website',
        'stock',
        'low_stock_threshold',
        'image',
        'band_id',
        'user_id',
        'owner',
        'tenant_id',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'cost' => 'decimal:2',
        'exp_date' => 'date',
        'length' => 'decimal:2',
        'width' => 'decimal:2',
        'height' => 'decimal:2',
        'weight' => 'decimal:2',
        'stock' => 'integer',
        'low_stock_threshold' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

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
        return $query->where('tenant_id', $tenantId);
    }

    public function getBlacklistedFields(): array
    {
        return array_merge($this->fillable, ['name', 'description']);
    }
}
