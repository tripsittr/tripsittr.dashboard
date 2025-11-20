<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Spatie\Permission\Models\Role;

class RolePolicy
{
    use HandlesAuthorization;

    /**
     * Allow Admins (by user type or role) to bypass granular checks.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->type === 'Admin' || $user->hasRole('Admin')) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any roles.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view roles') || $user->can('manage roles');
    }

    /**
     * Determine whether the user can view the role.
     */
    public function view(User $user, Role $role): bool
    {
        return $user->can('view roles') || $user->can('manage roles');
    }

    /**
     * Determine whether the user can create roles.
     */
    public function create(User $user): bool
    {
        return $user->can('manage roles');
    }

    /**
     * Determine whether the user can update the role.
     */
    public function update(User $user, Role $role): bool
    {
        return $user->can('manage roles');
    }

    /**
     * Determine whether the user can delete the role.
     */
    public function delete(User $user, Role $role): bool
    {
        return $user->can('manage roles');
    }

    /**
     * Determine whether the user can restore the role.
     */
    public function restore(User $user, Role $role): bool
    {
        return $user->can('manage roles');
    }

    /**
     * Determine whether the user can permanently delete the role.
     */
    public function forceDelete(User $user, Role $role): bool
    {
        return $user->can('manage roles');
    }

    /**
     * Determine whether the user can bulk delete roles.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('manage roles');
    }

    /**
     * Determine whether the user can bulk restore roles.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('manage roles');
    }

    /**
     * Determine whether the user can permanently bulk delete roles.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('manage roles');
    }

    /**
     * Determine whether the user can replicate a role.
     */
    public function replicate(User $user, Role $role): bool
    {
        return $user->can('manage roles');
    }

    /**
     * Determine whether the user can reorder roles.
     */
    public function reorder(User $user): bool
    {
        return $user->can('manage roles');
    }
}
