<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UserType;

class UserTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'Solo Artist', 'description' => 'An individual musician, singer, or performer who creates and releases music independently (e.g., vocalist, rapper, multi-instrumentalist).'],
            ['name' => 'Band Member', 'description' => 'An individual who is part of a band, contributing as a performer (e.g., vocalist, guitarist, drummer).'],
            ['name' => 'Songwriter', 'description' => 'Writes lyrics, melodies, or compositions, often collaborating with artists or producers.'],
            ['name' => 'Music Producer', 'description' => 'Oversees music production, including recording, mixing, or beat-making.'],
            ['name' => 'Manager', 'description' => 'Manages an artist’s or band’s career, handling bookings, contracts, and strategy.'],
            ['name' => 'Booking Agent', 'description' => 'Secures gigs, tours, or performance opportunities.'],
            ['name' => 'Publicist', 'description' => 'Manages media relations, press releases, or publicity campaigns.'],
            ['name' => 'Promoter', 'description' => 'Organizes and markets music events to drive attendance.'],
            ['name' => 'Sound Engineer', 'description' => 'Handles audio recording, mixing, or live sound production.'],
            ['name' => 'Tour Manager', 'description' => 'Coordinates logistics for tours, including travel and schedules.'],
            ['name' => 'Marketing Specialist', 'description' => 'Promotes artists via social media, digital campaigns, or branding.'],
            ['name' => 'Photographer', 'description' => 'Creates promotional photos or visual content for artists or events.'],
            ['name' => 'Videographer', 'description' => 'Produces music videos, live performance footage, or promotional video content.'],
            ['name' => 'Graphic Designer', 'description' => 'Designs album art, merchandise, or promotional visuals.'],
            ['name' => 'Music Publisher', 'description' => 'Manages licensing, royalties, or copyright for songs or compositions.'],
            ['name' => 'Venue Owner', 'description' => 'Manages a performance space and offers booking opportunities.'],
            ['name' => 'Music Instructor', 'description' => 'Provides lessons, mentorship, or workshops for aspiring artists.'],
            ['name' => 'Merchandiser', 'description' => 'Manages or sells artist merchandise like apparel or vinyl.'],
            ['name' => 'Event Organizer', 'description' => 'Coordinates platform-hosted events like showcases or Q&As.'],
            ['name' => 'Customer Support', 'description' => 'Assists users with technical or account-related issues.'],
            ['name' => 'Crowdfunding Organizer', 'description' => 'Manages crowdfunding campaigns to fund music projects.'],
        ];

        $names = collect($types)->pluck('name')->all();

        // Remove any previously seeded types not in the new list
        UserType::query()->whereNotIn('name', $names)->delete();

        // Upsert new set
        foreach ($types as $type) {
            UserType::updateOrCreate(
                ['name' => $type['name']],
                ['description' => $type['description']]
            );
        }
    }
}
