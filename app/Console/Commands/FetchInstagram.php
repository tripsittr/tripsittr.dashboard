<?php

namespace App\Console\Commands;

use App\Models\SocialAccount;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class FetchInstagram extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'social:instagram:fetch {social_account_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch latest Instagram media for a social account and log a short summary.';

    public function handle(): int
    {
        $id = (int) $this->argument('social_account_id');
        $account = SocialAccount::find($id);
        if (!$account) {
            $this->error('SocialAccount not found: ' . $id);
            return self::FAILURE;
        }

        if (!method_exists($account, 'instagramService') || !$account->instagramService()) {
            $this->error('This social account is not configured for Instagram/FB or missing instagramService.');
            return self::FAILURE;
        }

        try {
            $service = $account->instagramService();
            $media = $service->getMedia(10);
        } catch (\Throwable $e) {
            $this->error('API error: ' . $e->getMessage());
            Log::error('FetchInstagram error', ['exception' => $e, 'account_id' => $id]);
            return self::FAILURE;
        }

        $count = count($media);
        $this->info("Fetched {$count} media items for social_account={$id}");

        foreach ($media as $item) {
            $when = $item['timestamp'] ?? ($item['created_time'] ?? null);
            $this->line("- {$item['id']} ({$item['media_type']}) — " . substr(($item['caption'] ?? ''), 0, 80) . " — {$when}");
        }

        return self::SUCCESS;
    }
}
