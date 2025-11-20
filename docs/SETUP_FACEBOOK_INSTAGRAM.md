Setup: Facebook / Instagram integration

This document lists the environment variables, Facebook App scopes, and runtime setup required to enable Instagram Business features (reading media, insights, and publishing) and scheduled token refresh in this project.

Required environment variables (.env)

- FACEBOOK_APP_ID: your Facebook App ID
- FACEBOOK_APP_SECRET: your Facebook App Secret
- FACEBOOK_REDIRECT_URI: the OAuth callback URL (e.g. https://your-app.example.com/auth/facebook/callback)
- GRAPH_API_VERSION: (optional) Graph API version to target, e.g. v17.0 (defaults to v17.0)

Optional / recommended

- QUEUE_CONNECTION=database (or redis) — recommended if you publish/schedule posts
- APP_URL — should match the application public URL used for OAuth redirects

Facebook App scopes required for Instagram Business features

When linking an Instagram Business account (via Facebook OAuth) the user must grant the following scopes for full functionality:

- pages_show_list
- pages_read_engagement
- pages_read_user_content
- pages_manage_posts
- instagram_basic
- instagram_manage_insights
- instagram_content_publish

Notes on redirect URI

- Set the Redirect URI in your Facebook App settings to the `FACEBOOK_REDIRECT_URI` value above (example: https://your-app.example.com/auth/facebook/callback). When linking as "instagram" the app uses the Facebook driver and the same callback.

Token exchange & refresh

- The app contains a `FacebookTokenService` that exchanges short-lived tokens for long-lived tokens and re-exchanges to extend expiry.
- Tokens are cast as encrypted in the `SocialAccount` model and expire metadata is saved in `expires_at`.
- A scheduled Artisan command `social:facebook:refresh` will run daily (scheduled in the Console Kernel) and refresh tokens expiring within the next 7 days by default.

Scheduler / Cron

To run scheduled commands (including token refresh) add the following cron entry on your server (runs Laravel scheduler every minute):

* * * * * cd /path/to/project && php artisan schedule:run >> /dev/null 2>&1

Queue worker (if you use queued publishing)

If you publish content or do heavy API work, run a queue worker. Example using Supervisor (recommended for production):

[program:tripsittr-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/project/artisan queue:work --sleep=3 --tries=3 --timeout=60
autostart=true
autorestart=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/log/tripsittr/worker.log

Testing token exchange & discovery locally

1. Add the required FACEBOOK_* env vars to your `.env`.
2. Visit the Social Connections page in Filament and click Connect → Facebook (or Connect → Instagram to request IG scopes).
3. After callback, use the Discover button (in the settings UI) to populate `facebook_page_id` and `instagram_business_account_id`.
4. Run the refresh command manually to test token refresh:

   php artisan social:facebook:refresh --days=3650

Security notes

- Credentials (app secret, tokens) must be kept secure. Tokens are encrypted at rest by default.
- Implement token rotation if you wish; the provided service re-exchanges tokens to extend expiry.

If you'd like, I can:
- Add `FACEBOOK_*` keys to `.env.example` (non-secret placeholders).
- Add a Filament dashboard widget that surfaces token expiry dates.
- Implement queued discovery and refresh jobs with retries and backoff.

