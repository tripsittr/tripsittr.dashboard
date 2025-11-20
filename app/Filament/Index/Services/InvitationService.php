<?php
namespace App\Filament\Index\Services;

use App\Models\Invitation;
use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class InvitationService
{
    public function invite(Team $team, string $email, ?string $role, User $inviter): Invitation
    {
        return Invitation::create([
            'team_id' => $team->id,
            'email' => $email,
            'role' => $role,
            'invited_by' => $inviter->id,
        ]);
    }

    public function accept(Invitation $invitation, User $user): void
    {
        DB::transaction(function () use ($invitation, $user) {
            $team = Team::findOrFail($invitation->team_id);
            if (! $team->hasSeatAvailable()) {
                throw new \RuntimeException('No seats available for this team plan.');
            }
            $user->teams()->syncWithoutDetaching([$invitation->team_id]);
            app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($invitation->team_id);
            if ($invitation->role) {
                $role = \Spatie\Permission\Models\Role::where([
                    'name' => $invitation->role,
                    'team_id' => $invitation->team_id,
                    'guard_name' => 'web',
                ])->first();
                if ($role && ! $user->roles()->where('roles.id', $role->id)->exists()) {
                    $user->assignRole($role->name);
                    \App\Filament\Index\Services\LogActivity::record('role.assigned', 'User', $user->id, ['role' => $role->name], $invitation->team_id);
                }
            }
            $invitation->update(['accepted_at' => now()]);

            // Team-based billing: update subscription quantity directly on team.
            if (method_exists($team, 'subscription')) {
                $subscription = $team->subscription('default');
                if ($subscription) {
                    try {
                        $quantity = $team->usedSeats();
                        $subscription->updateQuantity($quantity);
                        \App\Services\LogActivity::record('subscription.quantity.updated', 'Team', $team->id, ['quantity' => $quantity], $team->id);
                    } catch (\Throwable $e) {
                        logger()->warning('Failed to update team subscription quantity: '.$e->getMessage());
                    }
                }
            }
        });
    }
}
