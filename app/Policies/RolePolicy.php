<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Role;
use App\Models\User;
use App\Enums\PermissionEnum;

class RolePolicy
{
    /**
     * Determine whether the user can view the list of roles.
     */
    public function viewAny(User $user): bool
    {
        return $user->can(PermissionEnum::ROLE_MANAGEMENT->value);
    }

    /**
     * Determine whether the user can create roles.
     */
    public function create(User $user): bool
    {
        return $user->can(PermissionEnum::ROLE_CREATE->value);
    }

    /**
     * Determine whether the user can update the given role.
     */
    public function update(User $user, Role $role): bool
    {
        if ($role->name === 'Super Admin') {
            return false;
        }

        return $user->can(PermissionEnum::ROLE_UPDATE->value);
    }

    /**
     * Determine whether the user can delete the given role.
     */
    public function delete(User $user, Role $role): bool
    {
        if ($role->name === 'Super Admin') {
            return false;
        }
        
        return $user->can(PermissionEnum::ROLE_DELETE->value);
    }
}
