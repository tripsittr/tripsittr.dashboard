<?php

namespace App\Policies;

use App\Models\Song;
use App\Models\User;

class SongPolicy
{
    public function update(User $user, Song $song): bool
    {
        if ($song->album && in_array($song->album->status, ['in_review','approved'])) {
            return false; // locked while album is in review or post-approved pre-release
        }
        return $user->type === 'Admin' || $song->team_id === optional($user->currentTeam)->id;
    }
}
