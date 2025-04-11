<?php

use App\Filament\User\Pages\UserDashboard;
use App\Http\Controllers\VenueController;
use App\Http\Controllers\InstagramController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;

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
        auth()->user()->integrations()->updateOrCreate(
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
