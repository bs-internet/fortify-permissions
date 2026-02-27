<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Permission;
use App\Models\User;

class PermissionPolicy
{
    /**
     * Determine whether the user can view the list of permissions.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('permission.management');
    }

    /**
     * Determine whether the user can create permissions.
     */
    public function create(User $user): bool
    {
        return $user->can('permission.create');
    }

    /**
     * Determine whether the user can update the given permission.
     */
    public function update(User $user, Permission $permission): bool
    {
        return $user->can('permission.update');
    }

    /**
     * Determine whether the user can delete the given permission.
     */
    public function delete(User $user, Permission $permission): bool
    {
        return $user->can('permission.delete');
    }
}
