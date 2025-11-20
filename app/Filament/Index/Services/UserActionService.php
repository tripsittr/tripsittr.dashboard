<?php
namespace App\Filament\Index\Services;

use App\Models\UserAction;

class UserActionService
{
    /**
     * Create a new user action.
     *
     * @param string $action
     * @param int $userId
     * @param int $teamId
     * @return UserAction
     */

    public function createUserAction(string $action, int $userId, int $teamId): UserAction
    {
        return UserAction::create([
            'action_type' => $action,
            'user_id' => $userId,
            'team_id' => $teamId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
