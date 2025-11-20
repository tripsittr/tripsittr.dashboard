<?php

namespace App\Services;

class OrderStatusTransition
{
    /**
     * Get allowed next status options for an order record.
     * @param \App\Models\Order $order
     * @return array
     */
    public static function nextOptions($order): array
    {
        // Example logic: allow all except current status
        $all = [
            'draft', 'pending', 'paid', 'fulfilled', 'shipped', 'cancelled', 'refunded', 'partial',
        ];
        $current = $order->status ?? 'draft';
        return array_values(array_filter($all, fn($s) => $s !== $current));
    }

    /**
     * Check if transition is allowed (stub for now)
     */
    public static function allowed($order, $new): bool
    {
        // Allow all transitions except to current
        return $order->status !== $new;
    }
}
