<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Auth;

class UpdateUserTeam
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user) {
            $tenantId = $request->segment(1);

            if ($tenantId && is_numeric($tenantId)) {
                if ($user->team_id != $tenantId) {
                    $user->update(['team_id' => $tenantId]);
                }
            }
        }

        return $next($request);
    }
}
