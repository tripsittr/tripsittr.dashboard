<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_id','inventory_item_id','catalog_item_id','description','quantity','unit_price','line_total'
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'line_total' => 'decimal:2',
        'quantity' => 'integer',
    ];

    public function order(): BelongsTo
    {
    return $this->belongsTo(\App\Models\Order::class);
    }

    public function inventoryItem(): BelongsTo
    {
    return $this->belongsTo(\App\Models\InventoryItem::class);
    }

    public function catalogItem(): BelongsTo
    {
    return $this->belongsTo(\App\Models\CatalogItem::class);
    }
}
