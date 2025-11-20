<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use App\Models\SongAnalytics;

class DummySongAnalyticsSeeder extends Seeder
{
    public function run()
    {
        // Create dummy analytics for multiple teams so charts have something to show
        $teams = [1, 2, 3];

        foreach ($teams as $teamId) {
            // create two imported_at timestamps to simulate multiple imports
            $timestamps = [Carbon::now()->subDays(30), Carbon::now()];

            foreach ($timestamps as $importedAt) {
                // generate 12 dummy songs per import
                for ($i = 1; $i <= 12; $i++) {
                    $streams = rand(0, 5000);
                    $streams_change = rand(-500, 500);
                    // Keep percentages in reasonable ranges so they fit decimal(8,4)
                    $streams_pct = round(rand(0, 5000) / 100, 4); // 0.00 - 50.00
                    $streams_change_pct = round(rand(-1000, 1000) / 100, 4); // -10.00 - 10.00

                    SongAnalytics::create([
                        'name' => 'Dummy Song ' . $i . ' (T' . $teamId . ')',
                        'external_id' => 900000 + $teamId * 1000 + $i,
                        'streams' => $streams,
                        'streams_pct' => $streams_pct,
                        'streams_change' => $streams_change,
                        'streams_change_pct' => $streams_change_pct,
                        'downloads' => rand(0, 2000),
                        'downloads_pct' => round(rand(0, 10000) / 100, 2),
                        'downloads_change' => rand(-200, 200),
                        'downloads_change_pct' => round(rand(-1000, 1000) / 10, 2),
                        'team_id' => $teamId,
                        'imported_at' => $importedAt,
                    ]);
                }
            }
        }
    }
}
