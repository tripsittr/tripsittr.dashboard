<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAction extends Model
{
    protected $fillable = [
        'action_type',
        'user_id',
        'team_id',
    ];
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    protected $table = 'user_actions';
    protected $primaryKey = 'id';

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function team()
    {
        return $this->belongsTo(Team::class);
    }


    public function getActionTypes(): array
    {
        return [
            'login' => 'Login',
            'logout' => 'Logout',
            'update_profile' => 'Update Profile',
            'change_password' => 'Change Password',
            'delete_account' => 'Delete Account',
            'create_song' => 'Create Song',
            'update_song' => 'Update Song',
            'delete_song' => 'Delete Song',
            'create_album' => 'Create Album',
            'update_album' => 'Update Album',
            'delete_album' => 'Delete Album',
            'create_playlist' => 'Create Playlist',
            'update_playlist' => 'Update Playlist',
            'delete_playlist' => 'Delete Playlist',
            'create_user' => 'Create User',
            'update_user' => 'Update User',
            'delete_user' => 'Delete User',
            'create_team' => 'Create Team',
            'update_team' => 'Update Team',
            'delete_team' => 'Delete Team',
            'create_event' => 'Create Event',
            'update_event' => 'Update Event',
            'delete_event' => 'Delete Event',
            'create_inventory_item' => 'Create Inventory Item',
            'update_inventory_item' => 'Update Inventory Item',
            'delete_inventory_item' => 'Delete Inventory Item',
        ];
    }
}
