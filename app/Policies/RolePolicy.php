<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Role;
use App\Models\User;

class RolePolicy
{
    /**
     * Determine whether the user can view the list of roles.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('role.management');
    }

    /**
     * Determine whether the user can create roles.
     */
    public function create(User $user): bool
    {
        return $user->can('role.create');
    }

    /**
     * Determine whether the user can update the given role.
     */
    public function update(User $user, Role $role): bool
    {
        return $user->can('role.update');
    }

    /**
     * Determine whether the user can delete the given role.
     */
    public function delete(User $user, Role $role): bool
    {
        return $user->can('role.delete');
    }
}
