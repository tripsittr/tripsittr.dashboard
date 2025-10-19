<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Filament\Facades\Filament;
use Spatie\Permission\PermissionRegistrar;

class SetPermissionTeamContext
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($tenant = Filament::getTenant()) {
            app(PermissionRegistrar::class)->setPermissionsTeamId($tenant->id);
        }
        return $next($request);
    }
}
