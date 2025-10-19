<?php

namespace App\Http\Middleware;

use App\Support\Settings;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnforceAppMaintenance
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Settings::get('maintenance_mode', false)) {
            $user = $request->user();
            $isAdmin = false;
            if ($user) {
                try {
                    $isAdmin = $user->roles()->whereIn('name',['Admin','admin'])->exists();
                } catch (\Throwable $e) {
                    $isAdmin = false; // conservative
                }
                if (! $isAdmin) {
                    // Fallback to legacy type flags if roles absent
                    $isAdmin = (isset($user->type) && in_array($user->type,['Admin','admin'], true)) ||
                        ($user->currentTeam && isset($user->currentTeam->type) && in_array($user->currentTeam->type,['Admin','admin'], true));
                }
            }
            if (! $isAdmin) {
                return response()->view('maintenance', [
                    'message' => 'We are currently performing maintenance. Please check back shortly.',
                ], 503);
            }
        }
        return $next($request);
    }
}
