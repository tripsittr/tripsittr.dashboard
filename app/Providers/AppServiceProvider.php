<?php

namespace App\Providers;

use App\Models\Album;
use App\Models\Event;
use App\Models\Song;
use App\Models\Team;
use App\Models\User;
use App\Observers\UserObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Laravel\Cashier\Cashier;

class AppServiceProvider extends ServiceProvider {
    /**
     * Register any application services.
     */
    public function register(): void {
        //
    }

    /**
     * Bootstrap any application services.
     */

    public function boot(): void {
        Blade::component('share-venue-modal', \App\View\Components\ShareVenueModal::class);
        Cashier::useCustomerModel(Team::class);
        Cashier::calculateTaxes();


        // Observers
        User::observe(UserObserver::class);
        Song::observe(\App\Observers\SongObserver::class);
        Album::observe(\App\Observers\AlbumObserver::class);
        Event::observe(\App\Observers\EventObserver::class);

    }
}
