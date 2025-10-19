<?php

namespace App\Policies;

use App\Models\Album;
use App\Models\User;

class AlbumPolicy
{
    public function view(User $user, Album $album): bool
    {
        return $user->type === 'Admin' || $album->team_id === optional($user->currentTeam)->id;
    }

    public function update(User $user, Album $album): bool
    {
        if (in_array($album->status, ['in_review','approved','released'])) { return false; }
        return $user->type === 'Admin' || $album->team_id === optional($user->currentTeam)->id;
    }

    public function submit(User $user, Album $album): bool
    {
        return in_array($album->status, ['draft','rejected']) && ($user->type === 'Admin' || $album->team_id === optional($user->currentTeam)->id);
    }

    public function approve(User $user, Album $album): bool
    {
        return $user->type === 'Admin' && $album->status === 'in_review';
    }

    public function reject(User $user, Album $album): bool
    {
        return $user->type === 'Admin' && $album->status === 'in_review';
    }
}
