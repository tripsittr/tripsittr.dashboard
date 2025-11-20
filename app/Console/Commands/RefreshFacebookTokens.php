<?php

namespace App\Console\Commands;

use App\Models\SocialAccount;
use App\Services\Social\FacebookTokenService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RefreshFacebookTokens extends Command
{
    protected $signature = 'social:facebook:refresh {--days=7 : Refresh tokens expiring within N days}';
    protected $description = 'Refresh Facebook/Instagram long-lived tokens that are expiring soon.';

    public function handle(FacebookTokenService $tokenService): int
    {
        $days = (int) $this->option('days');
        $threshold = now()->addDays($days);

        $this->info("Looking for social accounts expiring before {$threshold}");

        $accounts = SocialAccount::whereIn('provider', ['facebook', 'instagram'])
            ->whereNotNull('access_token')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', $threshold)
            ->get();

        $this->info('Found ' . $accounts->count() . ' accounts to refresh');

        foreach ($accounts as $acct) {
            $this->line('Refreshing account ' . $acct->id . ' (' . $acct->provider . ')');
            try {
                $resp = $tokenService->refreshToken($acct);
                if (! empty($resp['access_token'])) {
                    $acct->access_token = $resp['access_token'];
                    if (! empty($resp['expires_in'])) {
                        $acct->expires_at = now()->addSeconds($resp['expires_in']);
                    }
                    $acct->save();
                    $this->info('Refreshed ' . $acct->id);
                } else {
                    $this->warn('No access_token in response for ' . $acct->id);
                }
            } catch (\Throwable $e) {
                $this->error('Failed to refresh ' . $acct->id . ': ' . $e->getMessage());
                Log::error('RefreshFacebookTokens failed', ['exception' => $e, 'account_id' => $acct->id]);
            }
        }

        return self::SUCCESS;
    }
}
