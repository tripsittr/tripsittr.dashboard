<?php

namespace Database\Seeders;

use App\Models\TripLink;
use Illuminate\Database\Seeder;

class TripLinkSeeder extends Seeder
{
    public function run()
    {
        // Sample TripLink for team_id = 3
        TripLink::updateOrCreate([
            'team_id' => 3,
        ], [
            'title' => 'Sample Artist',
            'slug' => 'sample-artist',
            'bio' => 'This is a sample TripLink profile for testing purposes.',
            'links' => [
                ['label' => 'Official Site', 'url' => 'https://example.com', 'target' => '_blank'],
                ['label' => 'Latest Release', 'url' => 'https://example.com/release', 'target' => '_blank'],
            ],
            'social' => [
                ['platform' => 'Twitter', 'url' => 'https://twitter.com/example', 'handle' => '@example'],
                ['platform' => 'Instagram', 'url' => 'https://instagram.com/example', 'handle' => '@example'],
            ],
            'layout' => ['theme' => 'default'],
            'design' => ['background_color' => '#ffffff'],
            'published' => true,
        ]);
    }
}
