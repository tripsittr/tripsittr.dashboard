<?php

namespace App\Providers;

use App\Filament\Index\Observers;
use App\Models\Address;
use App\Models\Album;
use App\Models\CatalogItem;
use App\Models\Customer;
use App\Models\Event;
use App\Models\InventoryItem;
use App\Models\Invitation;
use App\Models\Knowledge;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Song;
use App\Models\Team;
use App\Models\TeamUser;
use App\Models\User;
use App\Models\Venue;
use App\Observers\BaseModelObserver;
use App\Policies\AlbumPolicy;
use App\Policies\RolePolicy;
use App\Policies\SongPolicy;
use App\Services\MailerProxy;
use Illuminate\Mail\Mailer;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Laravel\Cashier\Cashier;
use Spatie\Permission\Models\Role as SpatieRole;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

        $this->app->bind('path.public', function () {

            return base_path().'/public_html';

        });

    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blade::component('share-venue-modal', \App\View\Components\ShareVenueModal::class);
        Cashier::useCustomerModel(Team::class);
        Cashier::calculateTaxes();

        // Register mail view namespace if the vendor mail templates exist. This
        // prevents "No hint path defined for [mail]" errors when mail templates
        // are published under resources/views/vendor/mail.
        if (is_dir(resource_path('views/vendor/mail'))) {
            View::addNamespace('mail', resource_path('views/vendor/mail'));
        }

        // Wrap the framework mailer with a proxy that swallows and logs
        // exceptions during send/queue so that mail rendering/transport
        // failures don't break synchronous HTTP requests.
        try {
            $mailer = $this->app->make(Mailer::class);
            $this->app->instance(Mailer::class, new MailerProxy($mailer));
        } catch (\Throwable $e) {
            // If binding the proxy fails, just continue (we don't want to
            // break app boot).
        }

        // Observers (specific)
        User::observe(Observers\UserObserver::class);
        Song::observe(Observers\SongObserver::class);
        Album::observe(Observers\AlbumObserver::class);

        // Policies
        Gate::policy(Album::class, AlbumPolicy::class);
        Gate::policy(Song::class, SongPolicy::class);
        Gate::policy(SpatieRole::class, RolePolicy::class);
        Event::observe(Observers\EventObserver::class);

        // Generic observer for remaining models (skip those with custom logic or potential recursion issues)
        foreach ([
            // Address::class,
            CatalogItem::class,
            Customer::class,
            InventoryItem::class,
            Invitation::class,
            Knowledge::class,
            Order::class,
            OrderItem::class,
            // TeamUser::class,
            Venue::class,
        ] as $modelClass) {
            $modelClass::observe(BaseModelObserver::class);
        }

    }
}
