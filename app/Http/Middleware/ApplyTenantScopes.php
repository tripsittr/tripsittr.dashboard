<?php

namespace App\Http\Middleware;

use App\Models\Inventory;
use App\Models\InventoryItem;
use App\Models\Song;
use Closure;
use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ApplyTenantScopes
{
    public function handle(Request $request, Closure $next)
    {
        Song::addGlobalScope(
            fn(Builder $query) => $query->whereBelongsTo(Filament::getTenant()),
        );
        InventoryItem::addGlobalScope(
            fn(Builder $query) => $query->whereBelongsTo(Filament::getTenant()),
        );

        return $next($request);
    }
}
