@extends('layouts.guest')

@section('content')
<div class="min-h-screen bg-white dark:bg-gray-900 text-gray-800 dark:text-gray-100">
    <!-- Hero / Title -->
    <section class="py-12 md:py-16 border-b border-gray-200 dark:border-gray-800">
        <div class="mx-auto max-w-5xl px-4">
            <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight">TripSittr Dashboard — Product Documentation
            </h1>
            <p class="mt-3 text-lg text-gray-600 dark:text-gray-300">A Laravel + Filament application for managing music
                releases, integrations, and operations.</p>
            <div class="mt-6 flex gap-3">
                <a href="/"
                    class="inline-flex items-center rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">Go to
                    site</a>
                <a href="{{ route('login') }}"
                    class="inline-flex items-center rounded-lg border border-gray-300 dark:border-gray-700 px-4 py-2 hover:bg-gray-50 dark:hover:bg-gray-800">Sign
                    in</a>
            </div>
        </div>
    </section>

    <!-- Table of Contents -->
    <section class="py-8">
        <div class="mx-auto max-w-5xl px-4">
            <h2 class="text-xl font-semibold">Table of contents</h2>
            <nav class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-2 text-blue-600 dark:text-blue-400">
                <a href="#overview" class="hover:underline">Overview</a>
                <a href="#features" class="hover:underline">Key Features</a>
                <a href="#architecture" class="hover:underline">Architecture</a>
                <a href="#routes" class="hover:underline">Public Routes</a>
                <a href="#integrations" class="hover:underline">Integrations</a>
                <a href="#payments" class="hover:underline">Payments</a>
                <a href="#data-model" class="hover:underline">Data Model</a>
                <a href="#admin" class="hover:underline">Admin & RBAC</a>
                <a href="#ui" class="hover:underline">UI/UX</a>
                <a href="#local-dev" class="hover:underline">Local Development</a>
                <a href="#env" class="hover:underline">Environment Variables</a>
                <a href="#testing" class="hover:underline">Testing</a>
                <a href="#deployment" class="hover:underline">Deployment</a>
                <a href="#faq" class="hover:underline">FAQ</a>
            </nav>
        </div>
    </section>

    <!-- Overview -->
    <section id="overview" class="py-10 border-t border-gray-200 dark:border-gray-800">
        <div class="mx-auto max-w-5xl px-4">
            <h2 class="text-2xl font-bold">Overview</h2>
            <p class="mt-3 text-gray-700 dark:text-gray-300">
                TripSittr Dashboard is a Laravel application using Filament v3, Livewire, and Tailwind CSS. It manages
                music entities (albums, songs), connects to external services like Spotify and Facebook, and supports
                subscriptions using Stripe via Laravel Cashier. It includes an admin interface, import tools, widgets,
                and a dark mode friendly UI.
            </p>
        </div>
    </section>

    <!-- Key Features -->
    <section id="features" class="py-10 border-t border-gray-200 dark:border-gray-800">
        <div class="mx-auto max-w-5xl px-4 space-y-4">
            <h2 class="text-2xl font-bold">Key features</h2>
            <ul class="list-disc pl-6 space-y-2">
                <li>Music catalog management: Albums and Songs with relationships and metadata.</li>
                <li>Filament v3 admin UI for managing resources, pages, and widgets.</li>
                <li>Configurable Songs tile/grid widget with dark mode and adjustable grid size.</li>
                <li>Spotify OAuth flow to obtain and store refresh tokens for API access.</li>
                <li>Facebook linking via Laravel Socialite.</li>
                <li>Stripe subscriptions via Laravel Cashier with trial support.</li>
                <li>Pest for testing, Vite for asset bundling, Tailwind CSS for styling.</li>
            </ul>
        </div>
    </section>

    <!-- Architecture -->
    <section id="architecture" class="py-10 border-t border-gray-200 dark:border-gray-800">
        <div class="mx-auto max-w-5xl px-4">
            <h2 class="text-2xl font-bold">Architecture</h2>
            <div class="mt-3 grid md:grid-cols-2 gap-6 text-gray-700 dark:text-gray-300">
                <div>
                    <h3 class="font-semibold">Stack</h3>
                    <ul class="list-disc pl-6">
                        <li>Backend: Laravel</li>
                        <li>Admin/UI: Filament v3, Livewire</li>
                        <li>Frontend: Tailwind CSS, Vite</li>
                        <li>DB: MySQL/PostgreSQL (configure via .env)</li>
                    </ul>
                </div>
                <div>
                    <h3 class="font-semibold">Structure</h3>
                    <ul class="list-disc pl-6">
                        <li>App code under <code>app/</code> with Filament Resources, Pages, Widgets</li>
                        <li>Migrations and seeders under <code>database/</code></li>
                        <li>Blade views under <code>resources/views/</code></li>
                        <li>Configs under <code>config/</code> (auth, filament, services, cashier, etc.)</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Public Routes -->
    <section id="routes" class="py-10 border-t border-gray-200 dark:border-gray-800">
        <div class="mx-auto max-w-5xl px-4">
            <h2 class="text-2xl font-bold">Public routes</h2>
            <p class="mt-3 text-gray-700 dark:text-gray-300">Relevant routes defined in <code>routes/web.php</code>:</p>
            <ul class="mt-3 list-disc pl-6 space-y-2">
                <li><code>POST /extract-audio</code> — Process audio extraction via Filament page action.</li>
                <li><code>GET /spotify/login</code> — Start Spotify OAuth authorization.</li>
                <li><code>GET /spotify/callback</code> — Handle Spotify OAuth callback and store refresh token in
                    <code>storage/spotify_refresh_token.txt</code>.</li>
                <li><code>POST /venues/{venue}/share</code> and <code>DELETE /venue/{id}</code> — Venue actions.</li>
                <li><code>GET /auth/facebook</code> and <code>/auth/facebook/callback</code> — Link Facebook account via
                    Socialite.</li>
                <li><code>GET /subscribe</code> — Start Stripe checkout for subscription using Cashier.</li>
                <li><code>GET /docs</code> — This documentation page.</li>
            </ul>
        </div>
    </section>

    <!-- Integrations -->
    <section id="integrations" class="py-10 border-t border-gray-200 dark:border-gray-800">
        <div class="mx-auto max-w-5xl px-4 space-y-4">
            <h2 class="text-2xl font-bold">Integrations</h2>
            <div>
                <h3 class="font-semibold">Spotify</h3>
                <p class="text-gray-700 dark:text-gray-300">Uses OAuth Authorization Code flow. Configure in
                    <code>config/services.php</code>:</p>
                <pre class="mt-2 rounded-lg bg-gray-900 text-gray-100 p-4 overflow-auto"><code>SPOTIFY_CLIENT_ID=...
SPOTIFY_CLIENT_SECRET=...
SPOTIFY_REDIRECT_URI=${APP_URL}/spotify/callback</code></pre>
                <p class="mt-2">Scopes are passed via <code>/spotify/login</code>. The refresh token is stored at
                    <code>storage/spotify_refresh_token.txt</code> for reuse.</p>
            </div>
            <div>
                <h3 class="font-semibold">Facebook</h3>
                <p class="text-gray-700 dark:text-gray-300">Linked via Laravel Socialite. Configure
                    <code>FACEBOOK_CLIENT_ID</code>, <code>FACEBOOK_CLIENT_SECRET</code>, and redirect URI in your
                    <code>services.php</code> and application settings.</p>
            </div>
        </div>
    </section>

    <!-- Payments -->
    <section id="payments" class="py-10 border-t border-gray-200 dark:border-gray-800">
        <div class="mx-auto max-w-5xl px-4">
            <h2 class="text-2xl font-bold">Payments</h2>
            <p class="mt-3 text-gray-700 dark:text-gray-300">Stripe subscriptions are implemented with Laravel Cashier.
                See <code>config/cashier.php</code> and the <code>/subscribe</code> route. Update your Price ID and keys
                in <code>.env</code>.</p>
            <pre class="mt-2 rounded-lg bg-gray-900 text-gray-100 p-4 overflow-auto"><code>STRIPE_KEY=pk_test_...
STRIPE_SECRET=sk_test_...
CASHIER_PRICE=price_xxx
CASHIER_CURRENCY=usd</code></pre>
        </div>
    </section>

    <!-- Data Model -->
    <section id="data-model" class="py-10 border-t border-gray-200 dark:border-gray-800">
        <div class="mx-auto max-w-5xl px-4 space-y-4">
            <h2 class="text-2xl font-bold">Data model</h2>
            <p class="text-gray-700 dark:text-gray-300">Core entities include Albums and Songs. Songs include artist
                metadata fields to support primary, featured, producers, and composers.</p>
            <ul class="list-disc pl-6">
                <li>Songs table includes JSON fields: <code>primary_artists</code>, <code>featured_artists</code>,
                    <code>producers</code>, <code>composers</code>.</li>
                <li>Albums relate to Songs; additional relationships exist per the models in <code>app/Models</code>.
                </li>
            </ul>
        </div>
    </section>

    <!-- Admin & RBAC -->
    <section id="admin" class="py-10 border-t border-gray-200 dark:border-gray-800">
        <div class="mx-auto max-w-5xl px-4">
            <h2 class="text-2xl font-bold">Admin and access control</h2>
            <p class="mt-3 text-gray-700 dark:text-gray-300">The admin panel is powered by Filament v3.
                Roles/permissions are configured via standard Laravel policies and Filament configuration. Refer to
                <code>config/filament.php</code>, <code>app/Policies</code>, and Filament Resources under
                <code>app/Filament/Resources</code>.</p>
        </div>
    </section>

    <!-- UI/UX -->
    <section id="ui" class="py-10 border-t border-gray-200 dark:border-gray-800">
        <div class="mx-auto max-w-5xl px-4 space-y-4">
            <h2 class="text-2xl font-bold">UI/UX</h2>
            <ul class="list-disc pl-6 space-y-2 text-gray-700 dark:text-gray-300">
                <li>Responsive, dark-mode aware components built with Tailwind CSS.</li>
                <li>Album Songs Tile Widget: adjustable grid size, rounded cards, subtle borders for Filament look.</li>
                <li>Vite bundling with PostCSS and Tailwind for fast dev and production builds.</li>
            </ul>
        </div>
    </section>

    <!-- Local Development -->
    <section id="local-dev" class="py-10 border-t border-gray-200 dark:border-gray-800">
        <div class="mx-auto max-w-5xl px-4">
            <h2 class="text-2xl font-bold">Local development</h2>
            <ol class="mt-3 list-decimal pl-6 space-y-2 text-gray-700 dark:text-gray-300">
                <li>Copy <code>.env.example</code> to <code>.env</code> and set DB and service credentials.</li>
                <li>Install PHP deps: <code>composer install</code>.</li>
                <li>Install JS deps: <code>npm install</code> or <code>pnpm install</code>.</li>
                <li>Generate key: <code>php artisan key:generate</code>.</li>
                <li>Migrate and seed: <code>php artisan migrate --seed</code>.</li>
                <li>Link storage: <code>php artisan storage:link</code>.</li>
                <li>Run dev servers: <code>php artisan serve</code> and <code>npm run dev</code>.</li>
            </ol>
        </div>
    </section>

    <!-- Environment Variables -->
    <section id="env" class="py-10 border-t border-gray-200 dark:border-gray-800">
        <div class="mx-auto max-w-5xl px-4">
            <h2 class="text-2xl font-bold">Environment variables</h2>
            <div class="mt-3 grid md:grid-cols-2 gap-6 text-gray-700 dark:text-gray-300">
                <div>
                    <h3 class="font-semibold">Core</h3>
                    <ul class="list-disc pl-6">
                        <li>APP_NAME, APP_ENV, APP_KEY, APP_URL</li>
                        <li>DB_CONNECTION, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD</li>
                        <li>CACHE_DRIVER, QUEUE_CONNECTION, SESSION_DRIVER</li>
                        <li>FILESYSTEM_DISK</li>
                    </ul>
                </div>
                <div>
                    <h3 class="font-semibold">Services</h3>
                    <ul class="list-disc pl-6">
                        <li>STRIPE_KEY, STRIPE_SECRET, CASHIER_PRICE</li>
                        <li>SPOTIFY_CLIENT_ID, SPOTIFY_CLIENT_SECRET, SPOTIFY_REDIRECT_URI</li>
                        <li>FACEBOOK_CLIENT_ID, FACEBOOK_CLIENT_SECRET, FACEBOOK_REDIRECT_URI</li>
                        <li>MAIL_MAILER, MAIL_HOST, MAIL_PORT, MAIL_USERNAME, MAIL_PASSWORD, MAIL_ENCRYPTION,
                            MAIL_FROM_ADDRESS</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Testing -->
    <section id="testing" class="py-10 border-t border-gray-200 dark:border-gray-800">
        <div class="mx-auto max-w-5xl px-4">
            <h2 class="text-2xl font-bold">Testing</h2>
            <p class="mt-3 text-gray-700 dark:text-gray-300">This project uses Pest. Run tests with:</p>
            <pre class="mt-2 rounded-lg bg-gray-900 text-gray-100 p-4 overflow-auto"><code>php artisan test</code></pre>
        </div>
    </section>

    <!-- Deployment -->
    <section id="deployment" class="py-10 border-t border-gray-200 dark:border-gray-800">
        <div class="mx-auto max-w-5xl px-4">
            <h2 class="text-2xl font-bold">Deployment</h2>
            <ul class="list-disc pl-6 mt-3 text-gray-700 dark:text-gray-300">
                <li>Ensure <code>APP_URL</code> and production DB/queue/mail creds are set.</li>
                <li>Run migrations: <code>php artisan migrate --force</code></li>
                <li>Build assets: <code>npm run build</code></li>
                <li>Configure a queue worker for any queued jobs.</li>
                <li>Set up a scheduler (cron) to run <code>php artisan schedule:run</code> every minute if using
                    schedules.</li>
            </ul>
        </div>
    </section>

    <!-- FAQ -->
    <section id="faq" class="py-12 border-t border-gray-200 dark:border-gray-800">
        <div class="mx-auto max-w-5xl px-4 space-y-6">
            <h2 class="text-2xl font-bold">FAQ</h2>
            <div>
                <h3 class="font-semibold">Where is the admin panel?</h3>
                <p class="text-gray-700 dark:text-gray-300">The admin UI is provided by Filament. Refer to
                    <code>config/filament.php</code> for the panel path and settings.</p>
            </div>
            <div>
                <h3 class="font-semibold">How do I change the subscription plan?</h3>
                <p class="text-gray-700 dark:text-gray-300">Update the price ID in your <code>.env</code>
                    (<code>CASHIER_PRICE</code>) and adjust any plan logic in <code>routes/web.php</code> or relevant
                    controllers.</p>
            </div>
            <div>
                <h3 class="font-semibold">Where are Spotify tokens stored?</h3>
                <p class="text-gray-700 dark:text-gray-300">The refresh token from the OAuth flow is written to
                    <code>storage/spotify_refresh_token.txt</code> by the Spotify callback route.</p>
            </div>
        </div>
    </section>
</div>
@endsection