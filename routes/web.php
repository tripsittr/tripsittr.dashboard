<?php

use App\Filament\User\Pages\UserDashboard;
use App\Http\Controllers\VenueController;
use App\Http\Controllers\InstagramController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

Route::delete('/venue/{id}', [VenueController::class, 'destroy'])->name('venue.destroy');
Route::post('/venues/{venue}/share', [VenueController::class, 'share'])->name('venue.share');

Route::get('/instagram/redirect', [InstagramController::class, 'redirectToInstagram'])->name('instagram.redirect');
Route::get('/instagram/callback', [InstagramController::class, 'handleCallback'])->name('instagram.callback');

Route::get('/instagram/login', [InstagramController::class, 'redirectToInstagram'])->name('instagram.login');
Route::get('/instagram/callback', [InstagramController::class, 'handleInstagramCallback'])->name('instagram.callback');
Route::get('/instagram/analytics', [InstagramController::class, 'fetchAnalytics'])->name('instagram.analytics');

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
