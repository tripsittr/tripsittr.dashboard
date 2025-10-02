<?php

use App\Filament\Pages\ExtractAudio;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\VenueController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use Laravel\Socialite\Facades\Socialite;

Route::post('/extract-audio', [ExtractAudio::class, 'extractAudio'])->name('extract-audio.process');

// Public documentation landing page
Route::view('/docs', 'docs')->name('docs');

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

Route::get(
    '/auth/facebook/callback',
    function () {
        $user = Socialite::driver('facebook')->user();

        // Save the tokens to the database
        Auth::user()->integrations()->updateOrCreate(
            ['service' => 'facebook'],
            [
                'access_token' => $user->token,
                'refresh_token' => $user->refreshToken ?? null,
                'expires_at' => now()->addSeconds($user->expiresIn),
            ]
        );

        return redirect()->route('filament.pages.instagram-social-page')->with('success', 'facebook account linked successfully!');
    }
);

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
Route::post('login', [AuthenticatedSessionController::class, 'store'])->middleware('guest');
Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->middleware('auth')->name('logout');

// Provide a generic dashboard route used by tests, redirecting to Filament dashboard
Route::get('/dashboard', function () {
    // If using tenant-aware Filament, redirect to root which will route to default tenant dashboard
    return redirect()->intended('/');
})->name('dashboard');
