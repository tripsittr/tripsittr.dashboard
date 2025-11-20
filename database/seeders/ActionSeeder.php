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
            // Additional suggested actions
            ['action_title' => 'Invite Team Member',      'action_type' => 'invite_team_member'],
            ['action_title' => 'Remove Team Member',      'action_type' => 'remove_team_member'],
            ['action_title' => 'Assign Role',             'action_type' => 'assign_role'],
            ['action_title' => 'Revoke Role',             'action_type' => 'revoke_role'],

            ['action_title' => 'Unfeature Song',          'action_type' => 'unfeature_song'],
            ['action_title' => 'Release Album',           'action_type' => 'release_album'],
            ['action_title' => 'Unrelease Album',         'action_type' => 'unrelease_album'],
            ['action_title' => 'Add Track to Playlist',   'action_type' => 'add_track_to_playlist'],
            ['action_title' => 'Remove Track From Playlist','action_type' => 'remove_track_from_playlist'],

            ['action_title' => 'Create Order',            'action_type' => 'create_order'],
            ['action_title' => 'Update Order',            'action_type' => 'update_order'],
            ['action_title' => 'Cancel Order',            'action_type' => 'cancel_order'],
            ['action_title' => 'Refund Order',            'action_type' => 'refund_order'],
            ['action_title' => 'Capture Payment',         'action_type' => 'capture_payment'],
            ['action_title' => 'Create Customer',         'action_type' => 'create_customer'],
            ['action_title' => 'Update Customer',         'action_type' => 'update_customer'],
            ['action_title' => 'Delete Customer',         'action_type' => 'delete_customer'],
            ['action_title' => 'Create Catalog Item',     'action_type' => 'create_catalog_item'],
            ['action_title' => 'Update Catalog Item',     'action_type' => 'update_catalog_item'],
            ['action_title' => 'Delete Catalog Item',     'action_type' => 'delete_catalog_item'],
            ['action_title' => 'Export Orders',           'action_type' => 'export_orders'],
            ['action_title' => 'Export Customers',        'action_type' => 'export_customers'],

            ['action_title' => 'Publish Knowledge',       'action_type' => 'publish_knowledge'],
            ['action_title' => 'Unpublish Knowledge',     'action_type' => 'unpublish_knowledge'],
            ['action_title' => 'Archive Knowledge',       'action_type' => 'archive_knowledge'],

            ['action_title' => 'Create Approval',         'action_type' => 'create_approval'],
            ['action_title' => 'Approve Item',            'action_type' => 'approve_item'],
            ['action_title' => 'Reject Item',             'action_type' => 'reject_item'],

            ['action_title' => 'View Analytics',          'action_type' => 'view_analytics'],
            ['action_title' => 'Export Report',           'action_type' => 'export_report'],
        ];

        DB::table('action')->insert($actions);
    }
}
