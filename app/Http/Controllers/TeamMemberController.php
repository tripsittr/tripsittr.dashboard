<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Models\Team;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification as FacadeNotification;
use Filament\Notifications\Notification;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\TeamInvitationMail;

class TeamMemberController extends Controller
{
    public function invite(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'team_id' => ['required','integer','exists:teams,id'],
            'email' => ['required','email'],
            'role'  => ['required','in:Member,Manager,Admin'],
        ]);

        $user = Auth::user();
        // Resolve team explicitly from posted team_id (authoritative) then verify against Filament tenant if present
        $team = Team::find($data['team_id']);
        if (! $team) {
            abort(404,'Team not found');
        }
        // Ensure membership
        if(! $user->teams()->where('teams.id',$team->id)->exists()) {
            abort(403,'You are not a member of that team');
        }
        // Check Filament tenant mismatch (log only)
        $tenant = null; try { $tenant = \Filament\Facades\Filament::getTenant(); } catch (\Throwable $e) {}
        if($tenant instanceof Team && $tenant->id !== $team->id) {
            Log::warning('Invite team_id differs from Filament tenant', [
                'posted_team_id' => $team->id,
                'tenant_id' => $tenant->id,
                'user_id' => $user->id,
            ]);
        }

        // Prevent inviting existing member
        if ($team->users()->where('email',$data['email'])->exists()) {
            throw ValidationException::withMessages([
                'email' => 'That user is already a team member.',
            ]);
        }

        // Prevent duplicate active invitation
        $existing = Invitation::where('team_id',$team->id)
            ->where('email',$data['email'])
            ->whereNull('accepted_at')
            ->whereNull('revoked_at')
            ->first();
        if ($existing) {
            throw ValidationException::withMessages([
                'email' => 'An active invitation already exists for that email.',
            ]);
        }

        // Seat enforcement
        if (! $team->hasSeatAvailable()) {
            throw ValidationException::withMessages([
                'email' => 'Seat limit reached for this plan.',
            ]);
        }

        $correlationId = (string) \Illuminate\Support\Str::uuid();
        Log::info('Attempting to create invitation', [
            'correlation_id' => $correlationId,
            'team_id' => $team->id,
            'tenant_id' => ($tenant instanceof Team) ? $tenant->id : null,
            'email' => $data['email'],
            'role' => $data['role'],
            'user_id' => $user->id,
        ]);

        try {
            $invitation = Invitation::create([
                'team_id' => $team->id,
                'email'   => $data['email'],
                'role'    => $data['role'],
                'invited_by' => $user->id,
                'expires_at' => now()->addDays(14),
            ]);
        } catch(\Throwable $e) {
            Log::error('Invitation create failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            Notification::make()->title('Failed to create invitation')->danger()->send();
            return back()->withErrors(['email' => 'Failed to create invitation: '.$e->getMessage()]);
        }

        Log::info('Invitation created', [
            'correlation_id' => $correlationId,
            'invitation_id' => $invitation->id,
            'team_id' => $team->id,
            'tenant_id' => ($tenant instanceof Team) ? $tenant->id : null,
        ]);

        // Send email (queued if queue configured)
        try {
            Mail::to($invitation->email)->queue(new TeamInvitationMail($invitation));
        } catch(\Throwable $e) {
            logger()->warning('Failed sending invitation email: '.$e->getMessage());
        }

        Notification::make()->title('Invitation sent to '.$data['email'])->success()->send();

        return back();
    }

    public function updateRoles(Request $request, User $member): RedirectResponse
    {
        $validated = $request->validate([
            'role' => ['required','in:Member,Manager,Admin'],
            'permissions' => ['nullable','string','max:1000'],
        ]);

        // Normalize permissions into array
        $permissionNames = collect(preg_split('/[,\n]+/', (string)($validated['permissions'] ?? '')))
            ->map(fn($p) => trim($p))
            ->filter()
            ->unique()
            ->values();

        $user = Auth::user();
        $team = $user?->current_team;
        if (! $team) abort(403);

        app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($team->id);

        $selectedRole = $validated['role'];
        $requestedPermissions = $permissionNames;

        // --- Safeguard: prevent removing the last remaining Admin (self-demotion) ---
        $adminCount = $team->users()
            ->whereHas('roles', function($q) use ($team) {
                $q->where('name','Admin')->where('team_id',$team->id);
            })
            ->count();

        $isSelf = $member->id === $user->id;
        $isDemotingSelf = $isSelf && $selectedRole !== 'Admin' && $member->roles->contains(fn($r) => $r->name === 'Admin');
        if ($isDemotingSelf && $adminCount <= 1) {
            \Filament\Notifications\Notification::make()
                ->title('You are the only Admin and cannot demote yourself.')
                ->danger()
                ->send();
            return back();
        }
        // --- End safeguard ---

        // Ensure default roles exist for this team
        foreach (['Member','Manager','Admin'] as $rName) {
            Role::firstOrCreate([
                'name' => $rName,
                'team_id' => $team->id,
                'guard_name' => 'web'
            ]);
        }

        // Sync to a single role: remove any other team-scoped roles then assign selected if absent
        $currentRoles = $member->roles()->pluck('name');
        foreach ($currentRoles as $r) {
            if ($r !== $selectedRole) {
                $member->removeRole($r);
            }
        }
        if (! $member->roles()->where('name',$selectedRole)->exists()) {
            $member->assignRole($selectedRole);
        }

        // Auto-heal: ensure at least one Admin still exists after changes; if none, restore current user as Admin.
        $adminCountAfter = $team->users()
            ->whereHas('roles', function($q) use ($team) {
                $q->where('name','Admin')->where('team_id',$team->id);
            })
            ->count();
        if ($adminCountAfter === 0) {
            $adminRole = Role::firstOrCreate([
                'name' => 'Admin',
                'team_id' => $team->id,
                'guard_name' => 'web'
            ]);
            $user->assignRole($adminRole); // restore acting user
            \Filament\Notifications\Notification::make()
                ->title('Admin role restored to maintain at least one team admin.')
                ->warning()
                ->send();
        }

        // Handle direct permissions (team-scoped)
        // Create any missing permissions for this team
        $teamPermissionNames = Permission::where('team_id',$team->id)->pluck('name');
        $toCreate = $requestedPermissions->diff($teamPermissionNames);
        foreach ($toCreate as $permName) {
            Permission::create([
                'name' => $permName,
                'team_id' => $team->id,
                'guard_name' => 'web'
            ]);
        }

        // Current direct permissions (team scoped) for member
        $currentDirect = $member->permissions()->pluck('name');
        // Revoke those not requested
        foreach ($currentDirect as $perm) {
            if (! $requestedPermissions->contains($perm)) {
                $member->revokePermissionTo($perm);
            }
        }
        // Grant new requested
        foreach ($requestedPermissions as $perm) {
            if (! $member->permissions()->where('name',$perm)->exists()) {
                $member->givePermissionTo($perm);
            }
        }

        Notification::make()->title('Role & permissions updated')->success()->send();
        return back();
    }

    public function remove(Request $request, User $member): RedirectResponse
    {
        $user = Auth::user();
        $team = $user?->current_team;
        if (! $team) abort(403);
        if ($member->id === $user->id) {
            Notification::make()
                ->title('You cannot remove yourself')
                ->danger()
                ->send();
            return back();
        }

        // Safeguard: prevent removing the last remaining Admin
        app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($team->id);
        $isMemberAdmin = $member->roles()->where('name','Admin')->where('team_id',$team->id)->exists();
        if ($isMemberAdmin) {
            $adminCount = $team->users()
                ->whereHas('roles', function($q) use ($team) {
                    $q->where('name','Admin')->where('team_id',$team->id);
                })
                ->count();
            if ($adminCount <= 1) {
                Notification::make()
                    ->title('Cannot remove the only Admin from the team.')
                    ->danger()
                    ->send();
                return back();
            }
        }
        $team->users()->detach($member->id);
        // Adjust subscription quantity if team billing enabled
        if(method_exists($team,'subscription')) {
            $subscription = $team->subscription('default');
            if($subscription) {
                try {
                    $subscription->updateQuantity($team->usedSeats());
                    \App\Services\LogActivity::record('subscription.quantity.updated','Team',$team->id,[ 'quantity'=>$team->usedSeats() ], $team->id);
                } catch(\Throwable $e) {
                    logger()->warning('Failed to decrement subscription quantity after removal: '.$e->getMessage());
                }
            }
        }
        Notification::make()->title('Member removed')->success()->send();
        return back();
    }

    public function revokeInvitation(Request $request, Invitation $invitation): RedirectResponse
    {
        $user = Auth::user();
        $team = $user?->current_team;
        if(! $team || $invitation->team_id !== $team->id) abort(403);
        if($invitation->accepted_at) {
            Notification::make()->title('Invitation already accepted')->warning()->send();
            return back();
        }
        $invitation->markRevoked();
        Notification::make()->title('Invitation revoked')->warning()->send();
        return back();
    }

    public function resendInvitation(Request $request, Invitation $invitation): RedirectResponse
    {
        $user = Auth::user();
        $team = $user?->current_team;
        if(! $team || $invitation->team_id !== $team->id) abort(403);
        if($invitation->accepted_at) {
            Notification::make()->title('Invitation already accepted')->warning()->send();
            return back();
        }
        // Here you could trigger a Mail::to(...)
        \App\Services\LogActivity::record('invitation.resent','Invitation',$invitation->id,[ 'email'=>$invitation->email ], $invitation->team_id);
        // Resend email
        try {
            Mail::to($invitation->email)->queue(new TeamInvitationMail($invitation));
        } catch(\Throwable $e) {
            logger()->warning('Failed resending invitation email: '.$e->getMessage());
        }
        Notification::make()->title('Invitation resent')->success()->send();
        return back();
    }
}
