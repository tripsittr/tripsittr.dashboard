<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Realistic sample data
        $artistNames = [
            'Ava Carter',
            'Liam Brooks',
            'Mia Turner',
            'Noah Bennett',
            'Ella Hayes',
            'Lucas Reed',
            'Zoe Morgan',
            'Ethan Price',
            'Chloe Adams',
            'Mason Lee',
            'Harper Scott',
            'Logan Evans',
            'Layla King',
            'Jackson Hill',
            'Aria Green',
            'Elijah Young',
            'Scarlett Wood',
            'James Baker',
            'Grace Cooper',
            'Benjamin Ward'
        ];
        $albumTitles = [
            'Midnight Echoes',
            'Sunset Boulevard',
            'Neon Dreams',
            'Lost in Sound',
            'Golden Horizon',
            'Waves of Time',
            'Electric Heart',
            'Starlit Skies',
            'Parallel Universe',
            'Velvet Nights',
            'Crystal Visions',
            'Gravity',
            'Northern Lights',
            'Fire & Ice',
            'Eclipse',
            'Serenity',
            'Pulse',
            'Fragments',
            'Daybreak',
            'Reflections'
        ];
        $songTitles = [
            'Fading Memories',
            'Runaway',
            'Shadows',
            'Heartbeat',
            'Into the Blue',
            'Wildfire',
            'Gravity Falls',
            'Dreamcatcher',
            'Afterglow',
            'Lost & Found',
            'Mirage',
            'Echo Chamber',
            'Stardust',
            'Phoenix',
            'Moonlight',
            'Reverie',
            'Voyager',
            'Spectrum',
            'Awake',
            'Timeless'
        ];
        $genres = ['Rock', 'Pop', 'Jazz', 'Hip-Hop', 'Electronic', 'Indie', 'Folk', 'R&B', 'Country', 'Classical'];
        $itemNames = [
            'Guitar Strings',
            'Vinyl Record',
            'Concert Poster',
            'Band T-Shirt',
            'Drumsticks',
            'Microphone',
            'Headphones',
            'Music Book',
            'CD Album',
            'Stage Light',
            'Mixer',
            'Speaker',
            'Keyboard',
            'Bass Guitar',
            'Capo',
            'Guitar Pick',
            'Sheet Music',
            'Amp',
            'Pedal',
            'Earplugs'
        ];
        $eventNames = [
            'Summer Jam',
            'Winter Fest',
            'Indie Night',
            'Jazz in the Park',
            'Rock Revolution',
            'Pop Explosion',
            'Acoustic Evenings',
            'Hip-Hop Showcase',
            'Electronic Carnival',
            'Folk Gathering',
            'Country Roads',
            'Classical Gala',
            'Open Mic',
            'Charity Concert',
            'Album Release Party',
            'Music Awards',
            'Festival of Lights',
            'Underground Beats',
            'Retro Night',
            'Starlight Sessions'
        ];
        $venues = [
            'The Blue Note',
            'Sunset Arena',
            'Echo Hall',
            'Velvet Lounge',
            'Starlight Theater',
            'Pulse Club',
            'Gravity Grounds',
            'Serenity Gardens',
            'Firehouse Stage',
            'Crystal Ballroom',
            'Northern Pavilion',
            'Eclipse Center',
            'Daybreak Plaza',
            'Fragments Studio',
            'Reflections Venue',
            'Golden Hall',
            'Neon Dome',
            'Parallel Space',
            'Waves Amphitheater',
            'Lost & Found Loft'
        ];

        // Truncate tables before seeding
        DB::table('albums')->truncate();
        DB::table('songs')->truncate();
        DB::table('inventory_items')->truncate();
        DB::table('events')->truncate();

        // Seed albums
        $albums = [];
        for ($i = 1; $i <= 20; $i++) {
            $albums[] = [
                'title' => $albumTitles[$i - 1],
                'release_date' => now()->subYears(rand(0, 10))->subDays(rand(0, 365)),
                'team_id' => 1,
                'band_id' => 1,
                'artist_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('albums')->insert($albums);

        // Seed songs
        $songs = [];
        for ($i = 1; $i <= 20; $i++) {
            $songs[] = [
                'title' => $songTitles[$i - 1],
                'slug' => strtolower(str_replace(' ', '-', $songTitles[$i - 1])) . "-$i",
                'song_file' => 'song' . $i . '.mp3',
                'isrc' => 'ISRC' . rand(100000, 999999),
                'upc' => 'UPC' . rand(100000, 999999),
                'genre' => $genres[rand(0, count($genres) - 1)],
                'subgenre' => null,
                'artwork' => 'artwork' . $i . '.jpg',
                'release_date' => now()->subYears(rand(0, 10))->subDays(rand(0, 365)),
                'status' => 'unreleased',
                'visibility' => 'private',
                'distribution_status' => 'pending',
                'user_id' => 1,
                'team_id' => 1,
                'album_id' => $i,
                'primary_artists' => json_encode([$artistNames[$i - 1]]),
                'featured_artists' => json_encode([]),
                'producers' => json_encode([]),
                'composers' => json_encode([]),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('songs')->insert($songs);

        // Seed inventory_items
        $inventory = [];
        for ($i = 1; $i <= 20; $i++) {
            $inventory[] = [
                'sku' => 'SKU' . strtoupper(substr($itemNames[$i - 1], 0, 3)) . $i,
                'batch_number' => 'BATCH' . rand(1000, 9999),
                'barcode' => str_pad($i, 12, '0', STR_PAD_LEFT),
                'name' => $itemNames[$i - 1],
                'description' => 'High quality ' . strtolower($itemNames[$i - 1]) . ' for musicians and fans.',
                'price' => rand(10, 100),
                'cost' => rand(5, 50),
                'stock' => rand(0, 100),
                'low_stock_threshold' => 5,
                'team_id' => 1,
                'band_id' => 1,
                'user_id' => 1,
                'tenant_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('inventory_items')->insert($inventory);

        // Seed events
        $events = [];
        for ($i = 1; $i <= 20; $i++) {
            $start = now()->addDays(rand(1, 365));
            $end = (clone $start)->addHours(rand(2, 8));
            $events[] = [
                'name' => $eventNames[$i - 1],
                'description' => 'Join us for ' . $eventNames[$i - 1] . '!',
                'starts_at' => $start,
                'ends_at' => $end,
                'team_id' => 1,
                'author_id' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('events')->insert($events);
    }
}
