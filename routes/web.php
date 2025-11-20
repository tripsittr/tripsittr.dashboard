<?php

use App\Filament\Artists\Clusters\Extras\Pages\ExtractAudio;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\Music\SongAnalyticsController;
use App\Http\Controllers\TeamMemberController;
use App\Http\Controllers\TripLinkController;
use App\Http\Controllers\VenueController;
use App\Http\Middleware\UpdateUserTeam;
use Illuminate\Http\Request;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;

Route::post('/extract-audio', [ExtractAudio::class, 'extractAudio'])->name('extract-audio.process');

// (Temporary) debug route to verify team visibility & tenancy
Route::get('{tenant}/admin/teams/{record}/_debug', function ($tenant, $record) {
    $team = \App\Models\Team::withTrashed()->find($record);

    return response()->json([
        'tenant' => $tenant,
        'record' => $record,
        'exists' => (bool) $team,
        'team' => $team?->only(['id', 'name', 'type', 'deleted_at']),
        'all_ids' => \App\Models\Team::pluck('id'),
        'auth' => optional(Auth::user())->only(['id', 'email', 'type']),
    ]);
})->whereNumber('tenant')->whereNumber('record');

// Public documentation landing page
Route::view('/docs', 'docs')->name('docs');

// Public Terms of Service (static HTML)
Route::get('/terms-of-service', function () {
    return response()->file(public_path('terms-of-service.html'));
})->name('terms-of-service');

// Alternate paths to avoid single-segment conflicts and provide tenant-scoped access
Route::get('/legal/terms-of-service', function () {
    return response()->file(public_path('terms-of-service.html'));
})->name('terms-of-service.legal');

Route::get('/tos', function () {
    return response()->file(public_path('terms-of-service.html'));
})->name('tos');

Route::get('/{tenant}/terms-of-service', function ($tenant) {
    return response()->file(public_path('terms-of-service.html'));
})->whereNumber('tenant')->name('tenant.terms-of-service');

// Public Privacy Policy (static HTML)
Route::get('/privacy-policy', function () {
    return response()->file(public_path('privacy-policy.html'));
})->name('privacy-policy');
Route::get('/legal/privacy-policy', function () {
    return response()->file(public_path('privacy-policy.html'));
})->name('privacy-policy.legal');
Route::get('/privacy', function () {
    return response()->file(public_path('privacy-policy.html'));
})->name('privacy');
Route::get('/{tenant}/privacy-policy', function ($tenant) {
    return response()->file(public_path('privacy-policy.html'));
})->whereNumber('tenant')->name('tenant.privacy-policy');

// Dashboard (authenticated app) Privacy Policy
Route::get('/dashboard-privacy-policy', function () {
    return response()->file(public_path('dashboard-privacy-policy.html'));
})->name('dashboard-privacy-policy');
Route::get('/{tenant}/dashboard-privacy-policy', function ($tenant) {
    return response()->file(public_path('dashboard-privacy-policy.html'));
})->whereNumber('tenant')->name('tenant.dashboard-privacy-policy');

// Development-only debug route: render the Filament TripLink Layout page and
// return the fully rendered HTML so we can inspect top-level body children.
if (app()->environment('local') || config('app.debug')) {
    Route::get('/_debug/filament/triplink-layout-html', function (HttpRequest $request) {
        // NOTE: for local debugging we intentionally skip an auth check here so
        // the debug fetch can run from the local environment. This route is
        // already guarded to only register in local or debug environments.

        // Instantiate the Filament Page, call mount() to prepare form state,
        // then render the view to a string and return it.
        $page = app(\App\Filament\Artists\Clusters\TripLink\Pages\Layout::class);

        // Do NOT call mount() — some Page mount() implementations create or
        // modify DB records (firstOrCreate). We avoid side-effects by only
        // rendering the view here; the form state may be empty but the HTML
        // will be produced without creating models.
        // Try rendering the page view directly. Many Filament page views reference
        // `$this` (Livewire component) which isn't available when rendering a raw
        // view string. To avoid "Using $this when not in object context" errors
        // we fall back to a safe string-render: load the Blade file, replace
        // references to `$this->form` and `$this->form->getState()` with safe
        // placeholders, then compile the template string.

        try {
            $view = $page->render();
            $html = is_string($view) ? $view : $view->render();
        } catch (\Error $e) {
            // If rendering the component failed due to $this usage in Blade,
            // attempt a safe string render by loading and massaging the Blade
            // template directly. This intentionally removes the form output so
            // we can inspect top-level structure without side-effects.
            $bladePath = resource_path('views/filament/clusters/triplink/pages/layout.blade.php');
            if (file_exists($bladePath)) {
                $blade = file_get_contents($bladePath);
                // Replace $this->form->getState() with an empty array literal
                $blade = str_replace('$this->form->getState()', '[]', $blade);
                // Replace occurrences of the form display with an empty string variable
                $blade = str_replace('{{ $this->form }}', "{{ '' }}", $blade);
                $blade = str_replace('{{\$this->form}}', "{{ '' }}", $blade);

                // Provide a dummy $this with a form object so `$this->form->getState()`
                // calls inside the Blade template don't error when evaluated.
                $dummyThis = new class
                {
                    public $form;

                    public function __construct()
                    {
                        $this->form = new class
                        {
                            public function getState()
                            {
                                return [];
                            }

                            public function __toString()
                            {
                                return '';
                            }
                        };
                    }
                };

                $html = \Illuminate\Support\Facades\Blade::render($blade, ['this' => $dummyThis]);
            } else {
                throw $e;
            }
        }

        return response($html, 200)->header('Content-Type', 'text/html');
    })->name('debug.triplink.layout.html');
}

Route::get('/spotify/login', function () {
    $query = http_build_query([
        'client_id' => config('services.spotify.client_id'),
        'response_type' => 'code',
        'redirect_uri' => config('services.spotify.redirect'),
        'scope' => 'playlist-read-private user-read-email', // Add scopes as needed
    ]);

    return redirect('https://accounts.spotify.com/authorize?'.$query);
})->name('spotify.login');

Route::get('/spotify/callback', function () {
    $code = request('code');

    $response = Http::asForm()->post('https://accounts.spotify.com/api/token', [
        'grant_type' => 'authorization_code',
        'code' => $code,
        'redirect_uri' => config('services.spotify.redirect'),
        'client_id' => config('services.spotify.client_id'),
        'client_secret' => config('services.spotify.client_secret'),
    ]);

    $data = $response->json();

    // Log the full response for debugging
    logger('Spotify API Response:', $data);

    if (! isset($data['refresh_token'])) {
        return response()->json([
            'error' => 'Refresh token not returned. Check your Spotify app settings and scopes.',
            'response' => $data,
        ]);
    }

    // Save the refresh token to a file
    file_put_contents(storage_path('spotify_refresh_token.txt'), $data['refresh_token']);

    return 'Refresh token saved successfully!';
})->name('spotify.callback');

Route::delete('/venue/{id}', [VenueController::class, 'destroy'])->name('venue.destroy');
Route::post('/venues/{venue}/share', [VenueController::class, 'share'])->name('venue.share');

Route::get('/auth/facebook', function () {
    return Socialite::driver('facebook')->redirect();
})->name('facebook.link');

// Generic social auth link and callback handlers for supported providers.
Route::get('/auth/{provider}', function ($provider) {
    $allowed = ['facebook', 'instagram', 'twitter'];
    if (! in_array($provider, $allowed, true)) {
        abort(404);
    }
    // Instagram linking uses the Facebook OAuth driver but we label it 'instagram'
    if ($provider === 'instagram') {
        // request the extended scopes needed for Instagram Business features
        /** @var \Laravel\Socialite\Two\AbstractProvider $socialDriver */
        // If you maintain a separate Facebook App for Instagram, allow overriding
        // the Facebook driver credentials from environment variables so we can
        // use the Instagram app's App ID/Secret for the OAuth flow.
        if (env('INSTAGRAM_APP_ID') && env('INSTAGRAM_APP_SECRET')) {
            config([
                'services.facebook.client_id' => env('INSTAGRAM_APP_ID'),
                'services.facebook.client_secret' => env('INSTAGRAM_APP_SECRET'),
                // Allow a dedicated INSTAGRAM_OAUTH_REDIRECT override, otherwise
                // fall back to the normal services.facebook.redirect value.
                'services.facebook.redirect' => env('INSTAGRAM_OAUTH_REDIRECT', config('services.facebook.redirect')),
            ]);
        }

        $socialDriver = Socialite::driver('facebook');

        return $socialDriver
            ->scopes([
                'pages_show_list',
                'pages_read_engagement',
                'pages_read_user_content',
                'pages_manage_posts',
                'instagram_basic',
                'instagram_manage_insights',
                'instagram_content_publish',
            ])
            ->with(['auth_type' => 'rerequest'])
            ->redirect();
    }

    return Socialite::driver($provider)->redirect();
})->name('social.link');

Route::get('/auth/{provider}/callback', function ($provider) {
    $allowed = ['facebook', 'instagram', 'twitter'];
    if (! in_array($provider, $allowed, true)) {
        abort(404);
    }

    // Instagram uses the Facebook driver but is stored under provider 'instagram'.
    // Attempt the normal (stateful) flow first; if the state cookie is missing or
    // mismatched (InvalidStateException), retry in stateless mode as a fallback.
    // Try the normal (stateful) Socialite flow first. If it fails due to missing
    // state or a token exchange error (expired auth code), catch and handle
    // gracefully so the user can retry the connect flow instead of seeing a 500.
    try {
        if ($provider === 'instagram') {
            // If a separate Instagram/Facebook app ID+secret are present, ensure
            // the Socialite Facebook driver uses them for this exchange.
            if (env('INSTAGRAM_APP_ID') && env('INSTAGRAM_APP_SECRET')) {
                config([
                    'services.facebook.client_id' => env('INSTAGRAM_APP_ID'),
                    'services.facebook.client_secret' => env('INSTAGRAM_APP_SECRET'),
                    'services.facebook.redirect' => env('INSTAGRAM_OAUTH_REDIRECT', config('services.facebook.redirect')),
                ]);
            }

            $socialUser = Socialite::driver('facebook')->user();
            $persistProvider = 'instagram';
        } else {
            $socialUser = Socialite::driver($provider)->user();
            $persistProvider = $provider;
        }
    } catch (\Laravel\Socialite\Two\InvalidStateException $e) {
        // Session state missing/mismatched — retry once in stateless mode.
        try {
            if ($provider === 'instagram') {
                $socialUser = Socialite::driver('facebook')->stateless()->user();
                $persistProvider = 'instagram';
            } else {
                $socialUser = Socialite::driver($provider)->stateless()->user();
                $persistProvider = $provider;
            }
        } catch (\Throwable $e) {
            logger()->warning('Socialite stateless retry failed', ['provider' => $provider, 'exception' => $e]);

            return redirect()->route('social.link', ['provider' => $provider])->with('error', 'OAuth state validation failed. Please try connecting again.');
        }
    } catch (\GuzzleHttp\Exception\RequestException $e) {
        // Guzzle-level errors during the token exchange (e.g. expired code)
        $body = method_exists($e, 'getResponse') && $e->getResponse() ? $e->getResponse()->getBody()->getContents() : null;
        logger()->warning('Socialite/Guzzle token exchange error', ['provider' => $provider, 'message' => $e->getMessage(), 'response' => $body]);

        // If the authorization code expired, ask the user to try again.
        if (is_string($body) && str_contains($body, 'This authorization code has expired')) {
            return redirect()->route('social.link', ['provider' => $provider])->with('error', 'Authorization code expired — please try connecting again.');
        }

        return back()->with('error', 'Failed to complete OAuth flow: '.($e->getMessage() ?: 'unknown error'));
    } catch (\Throwable $e) {
        // Generic fallback — log and show a friendly message.
        logger()->error('Socialite callback failed', ['provider' => $provider, 'exception' => $e]);

        return back()->with('error', 'An error occurred while linking your account. Please try again.');
    }

    // Ensure there's an authenticated user to attach the social account to.
    $user = Auth::user();
    if (! $user) {
        // Store a minimal pending payload in session so the user can re-try linking
        // after signing in. We avoid storing the full Socialite user object.
        session(['social_oauth_pending' => [
            'provider' => $persistProvider,
            'provider_id' => $socialUser->getId(),
            'email' => $socialUser->getEmail(),
            'name' => $socialUser->getName(),
            'avatar' => $socialUser->getAvatar(),
        ]]);

        logger()->warning('Social callback received but no authenticated user present; saved pending payload to session', ['provider' => $persistProvider]);

        // Redirect to a login URL path (avoid referencing a named route that may not exist)
        return redirect('/login')->with('info', 'Please sign in to link your social account. After signing in, return to Social Connections to finish linking.');
    }

    // Persist into social_accounts table for the authenticated user
    $acct = $user->socialAccounts()->updateOrCreate(
        ['provider' => $persistProvider],
        [
            'provider_id' => $socialUser->getId(),
            'access_token' => $socialUser->token ?? null,
            'refresh_token' => $socialUser->refreshToken ?? null,
            'expires_at' => isset($socialUser->expiresIn) ? now()->addSeconds($socialUser->expiresIn) : null,
            'meta' => [
                'name' => $socialUser->getName(),
                'nickname' => $socialUser->getNickname(),
                'email' => $socialUser->getEmail(),
                'avatar' => $socialUser->getAvatar(),
            ],
        ]
    );

    // Immediately attempt to exchange a short-lived token for a long-lived token
    // when using Facebook (or Instagram via the Facebook driver). This replaces
    // the access_token with the long-lived value and stores the expires_at.
    try {
        if (! empty($acct->access_token) && in_array($persistProvider, ['facebook', 'instagram'], true)) {
            $tokenService = app(\App\Services\Social\FacebookTokenService::class);
            $resp = $tokenService->exchangeForLongLivedToken($acct);

            if (! empty($resp['access_token'])) {
                $acct->access_token = $resp['access_token'];
                if (isset($resp['expires_in'])) {
                    $acct->expires_at = now()->addSeconds((int) $resp['expires_in']);
                }
                $acct->save();
            }
        }
    } catch (\Throwable $e) {
        logger()->warning('Failed to exchange Facebook short-lived token for long-lived token', ['exception' => $e, 'acct_id' => $acct->id ?? null]);
        // Don't break the user flow if exchange fails; continue with discovery attempt
    }

    // If linking Instagram, attempt to discover the Facebook Page and Instagram Business Account
    if ($persistProvider === 'instagram' && $acct && $acct->access_token) {
        try {
            $apiVersion = config('services.facebook.graph_version', env('GRAPH_API_VERSION', 'v17.0'));
            $resp = Http::acceptJson()->get("https://graph.facebook.com/{$apiVersion}/me/accounts", [
                'access_token' => $acct->access_token,
                'fields' => 'id,name,instagram_business_account',
            ]);

            if ($resp->successful()) {
                $data = $resp->json();
                foreach ($data['data'] ?? [] as $page) {
                    if (! empty($page['instagram_business_account']['id'])) {
                        $acct->facebook_page_id = $page['id'];
                        $acct->instagram_business_account_id = $page['instagram_business_account']['id'];
                        $acct->save();
                        break;
                    }
                }
            }
        } catch (\Throwable $e) {
            logger()->warning('Failed to discover FB page / IG account during instagram link: '.$e->getMessage(), ['exception' => $e]);
        }
    }

    // Attempt to redirect back into the Filament Social Media Profile page if available.
    if (\Illuminate\Support\Facades\Route::has('filament.pages.social-media.profile')) {
        return redirect()->route('filament.pages.social-media.profile')->with('success', "{$persistProvider} account linked successfully!");
    }

    // Fallback: redirect to the app dashboard route
    return redirect()->route('dashboard')->with('success', "{$persistProvider} account linked successfully!");
})->name('social.callback');

Route::post('/auth/{provider}/unlink', function ($provider) {
    $allowed = ['facebook', 'instagram', 'twitter'];
    if (! in_array($provider, $allowed, true)) {
        abort(404);
    }

    $acct = Auth::user()->socialAccounts()->where('provider', $provider)->first();
    if ($acct) {
        $acct->delete();
    }

    return back()->with('success', "$provider account unlinked");
})->name('social.unlink');

// Manual discovery endpoint: attempts to populate facebook_page_id and instagram_business_account_id
Route::post('/social-accounts/{id}/discover', function ($id) {
    $acct = Auth::user()->socialAccounts()->where('id', $id)->first();
    if (! $acct) {
        abort(404);
    }

    if (empty($acct->access_token)) {
        return back()->with('error', 'No access token available for this account.');
    }

    try {
        $apiVersion = config('services.facebook.graph_version', env('GRAPH_API_VERSION', 'v17.0'));
        $resp = Http::acceptJson()->get("https://graph.facebook.com/{$apiVersion}/me/accounts", [
            'access_token' => $acct->access_token,
            'fields' => 'id,name,instagram_business_account',
        ]);

        if ($resp->successful()) {
            $data = $resp->json();
            foreach ($data['data'] ?? [] as $page) {
                if (! empty($page['instagram_business_account']['id'])) {
                    $acct->facebook_page_id = $page['id'];
                    $acct->instagram_business_account_id = $page['instagram_business_account']['id'];
                    $acct->save();

                    return back()->with('success', 'Discovered Facebook Page and Instagram Business Account.');
                }
            }
        }

        return back()->with('warning', 'No Instagram Business Account found on any connected Page.');
    } catch (\Throwable $e) {
        logger()->warning('Discovery failed: '.$e->getMessage(), ['exception' => $e, 'acct' => $acct->id]);

        return back()->with('error', 'Discovery failed: '.$e->getMessage());
    }
})->middleware('auth')->name('social.discover');

Route::get('/subscribe', function (Request $request) {
    return $request->user()
        ->newSubscription('default', 'price_1QQcIiC41PwR2k7V6jCjh8rX')
        ->trialDays(15)
        ->allowPromotionCodes()
        ->checkout([
            'success_url' => route('users.dashboard'),
            'cancel_url' => route('users.dashboard'),
        ]);
});

// Auth endpoints required by Feature tests
// Route::post('login', [AuthenticatedSessionController::class, 'store'])->middleware('guest');
// Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth')->name('logout');
// Provide a generic dashboard route used by tests, redirecting to Filament dashboard
Route::get('/dashboard', function () {
    // If using tenant-aware Filament, redirect to root which will route to default tenant dashboard
    return redirect()->intended('/');
})->name('dashboard');
Route::get('/invitations/{token}', [InvitationController::class, 'show'])->name('invitations.show');
Route::post('/invitations/{token}/accept', [InvitationController::class, 'accept'])->name('invitations.accept');

// Team member management (non-Filament form posts inside modals)
Route::middleware(['auth'])->group(function () {
    Route::post('/team/invite', [TeamMemberController::class, 'invite'])->name('team.invite');
    Route::put('/team/member/{member}/roles', [TeamMemberController::class, 'updateRoles'])->name('team.member.roles');
    Route::delete('/team/member/{member}', [TeamMemberController::class, 'remove'])->name('team.member.remove');
    Route::post('/team/invitation/{invitation}/revoke', [TeamMemberController::class, 'revokeInvitation'])->name('team.invitation.revoke');
    Route::post('/team/invitation/{invitation}/resend', [TeamMemberController::class, 'resend'])->name('team.invitation.resend');

});

// Preview endpoint for live layout preview in the Layout editor
Route::middleware(['auth'])->group(function () {
    Route::post('/triplink/layout/preview', [\App\Http\Controllers\TripLinkPreviewController::class, 'preview'])
        ->middleware([UpdateUserTeam::class, \Filament\Http\Middleware\Authenticate::class])
        ->name('triplink.layout.preview');
});

// Song Analytics CSV import (authenticated + tenant context)
Route::middleware(['auth'])->group(function () {
    Route::post('/artists/music/song-analytics/import', [SongAnalyticsController::class, 'import'])
        ->middleware([UpdateUserTeam::class, \Filament\Http\Middleware\Authenticate::class])
        ->name('music.song-analytics.import');
    Route::get('/artists/music/song-analytics/export', [SongAnalyticsController::class, 'export'])
        ->middleware([UpdateUserTeam::class, \Filament\Http\Middleware\Authenticate::class])
        ->name('music.song-analytics.export');
});

// TripLinks public endpoint (admin actions are handled via Filament pages)
Route::get('/u/{slug}', [TripLinkController::class, 'show'])->name('triplinks.show');

// Simple preview page for the Layout editor — reads session state and renders
// the public TripLink template for a quick visual check.
Route::get('/triplink/preview', function () {
    $data = session('triplink_preview', []);

    $trip = new \App\Models\TripLink;
    // Use forceFill to avoid static analysis errors when assigning attributes on a fresh model
    $trip->forceFill([
        'layout' => $data['layout'] ?? [],
        'title' => $data['title'] ?? 'Preview',
        'bio' => $data['bio'] ?? '',
        'design' => $data['design'] ?? [],
    ]);

    return view('triplinks.show', compact('trip'));
})->name('triplink.preview');
