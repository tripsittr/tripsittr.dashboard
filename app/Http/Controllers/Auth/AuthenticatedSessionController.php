<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // If a social OAuth pending payload exists from a prior callback (user
        // completed OAuth but was not authenticated), attach a placeholder
        // SocialAccount to the now-authenticated user so the admin can finish
        // the linking in the Social Connections settings UI.
        $pending = $request->session()->pull('social_oauth_pending');
        if (! empty($pending)) {
            try {
                $user = $request->user();
                if ($user) {
                    $meta = [
                        'name' => $pending['name'] ?? null,
                        'email' => $pending['email'] ?? null,
                        'avatar' => $pending['avatar'] ?? null,
                    ];

                    $user->socialAccounts()->updateOrCreate(
                        ['provider' => $pending['provider']],
                        [
                            'provider_id' => $pending['provider_id'] ?? null,
                            'meta' => $meta,
                        ]
                    );
                }
            } catch (\Throwable $e) {
                logger()->warning('Failed to auto-attach pending social payload after login', ['exception' => $e]);
            }
        }

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
