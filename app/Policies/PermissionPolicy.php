<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Permission;
use App\Models\User;
use App\Enums\PermissionEnum;

class PermissionPolicy
{
    /**
     * Determine whether the user can view the list of permissions.
     */
    public function viewAny(User $user): bool
    {
        return $user->can(PermissionEnum::PERMISSION_MANAGEMENT->value);
    }

    /**
     * Determine whether the user can update the given permission.
     */
    public function update(User $user, Permission $permission): bool
    {
        return $user->can(PermissionEnum::PERMISSION_UPDATE->value);
    }
}
