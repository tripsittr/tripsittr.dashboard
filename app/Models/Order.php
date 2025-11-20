<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'team_id','customer_id','reference','status','placed_at',
    'shipping_first_name','shipping_last_name','shipping_company',
    'shipping_email','shipping_phone',
        'shipping_address_line1','shipping_address_line2','shipping_city','shipping_region','shipping_postal_code','shipping_country',
        'shipping_carrier','shipping_method','shipping_saturday_delivery',
        'shipping_reference','shipping_reference_2','tracking_number','shipping_cost',
        'subtotal','tax_total','total','notes'
    ];

    protected $casts = [
        'placed_at' => 'datetime',
    'shipping_cost' => 'decimal:2',
    'shipping_saturday_delivery' => 'boolean',
        'subtotal' => 'decimal:2',
        'tax_total' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function team(): BelongsTo
    {
    return $this->belongsTo(\App\Models\Team::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            if (empty($order->team_id) && Filament::getTenant()) {
                $order->team_id = Filament::getTenant()->id;
            }
        });

        static::saving(function (Order $order) {
            $teamTax = $order->team?->tax_rate ?? 0;
            $subtotal = $order->items->sum('line_total');
            $order->subtotal = $subtotal;
            $order->tax_total = round(($subtotal * ($teamTax / 100)), 2);
            $order->total = $subtotal + ($order->shipping_cost ?? 0) + $order->tax_total;
        });

        static::updated(function (Order $order) {
            if (! $order->stock_deducted && in_array($order->status, ['fulfilled', 'shipped'])) {
                DB::transaction(function () use ($order) {
                    foreach ($order->items as $item) {
                        if ($item->inventoryItem) {
                            $item->inventoryItem->decrement('stock', $item->quantity);
                            \App\Services\LogActivity::record('inventory.decrement','InventoryItem',$item->inventory_item_id,[ 'quantity' => $item->quantity ], $order->team_id);
                        }
                    }
                    $order->stock_deducted = true;
                    $order->saveQuietly();
                });
            }
            \App\Services\LogActivity::record('order.updated','Order',$order->id,$order->getChanges(), $order->team_id);
        });
    }
}
