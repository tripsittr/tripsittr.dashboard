<?php
namespace App\Filament\Index\Services;

use App\Models\Order;

class OrderStatusTransition
{
    protected static array $map = [
        'draft' => ['pending','cancelled'],
        'pending' => ['paid','cancelled'],
        'paid' => ['fulfilled','refunded','cancelled','partial'],
        'partial' => ['fulfilled','refunded','cancelled'],
        'fulfilled' => ['shipped'],
        'shipped' => [],
        'cancelled' => [],
        'refunded' => [],
    ];

    public static function allowed(Order $order, string $to): bool
    {
        return in_array($to, self::$map[$order->status] ?? [], true);
    }

    public static function nextOptions(Order $order): array
    {
        return self::$map[$order->status] ?? [];
    }
}
