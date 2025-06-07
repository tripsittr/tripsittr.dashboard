<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $actions = [
            // User actions
            ['action_title' => 'Login', 'action_type' => 'login'],
            ['action_title' => 'Logout', 'action_type' => 'logout'],
            ['action_title' => 'Update Profile', 'action_type' => 'update_profile'],
            ['action_title' => 'Change Password', 'action_type' => 'change_password'],
            ['action_title' => 'Delete Account', 'action_type' => 'delete_account'],

            // Song actions
            ['action_title' => 'Create Song', 'action_type' => 'create_song'],
            ['action_title' => 'Update Song', 'action_type' => 'update_song'],
            ['action_title' => 'Delete Song', 'action_type' => 'delete_song'],

            // Album actions
            ['action_title' => 'Create Album', 'action_type' => 'create_album'],
            ['action_title' => 'Update Album', 'action_type' => 'update_album'],
            ['action_title' => 'Delete Album', 'action_type' => 'delete_album'],

            // Playlist actions
            ['action_title' => 'Create Playlist', 'action_type' => 'create_playlist'],
            ['action_title' => 'Update Playlist', 'action_type' => 'update_playlist'],
            ['action_title' => 'Delete Playlist', 'action_type' => 'delete_playlist'],

            // User management actions
            ['action_title' => 'Create User', 'action_type' => 'create_user'],
            ['action_title' => 'Update User', 'action_type' => 'update_user'],
            ['action_title' => 'Delete User', 'action_type' => 'delete_user'],

            // Team actions
            ['action_title' => 'Create Team', 'action_type' => 'create_team'],
            ['action_title' => 'Update Team', 'action_type' => 'update_team'],
            ['action_title' => 'Delete Team', 'action_type' => 'delete_team'],

            // Event actions
            ['action_title' => 'Create Event', 'action_type' => 'create_event'],
            ['action_title' => 'Update Event', 'action_type' => 'update_event'],
            ['action_title' => 'Delete Event', 'action_type' => 'delete_event'],

            // Inventory actions
            ['action_title' => 'Create Inventory Item', 'action_type' => 'create_inventory_item'],
            ['action_title' => 'Update Inventory Item', 'action_type' => 'update_inventory_item'],
            ['action_title' => 'Delete Inventory Item', 'action_type' => 'delete_inventory_item'],

            // Knowledge actions
            ['action_title' => 'Create Knowledge', 'action_type' => 'create_knowledge'],
            ['action_title' => 'Update Knowledge', 'action_type' => 'update_knowledge'],
            ['action_title' => 'Delete Knowledge', 'action_type' => 'delete_knowledge'],

            // Venue actions
            ['action_title' => 'Create Venue', 'action_type' => 'create_venue'],
            ['action_title' => 'Update Venue', 'action_type' => 'update_venue'],
            ['action_title' => 'Delete Venue', 'action_type' => 'delete_venue'],
        ];

        DB::table('action')->insert($actions);
    }
}
