<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Unit;
use App\Models\User;
use App\Enums\PermissionEnum;

class UnitPolicy
{
    /**
     * Determine whether the user can view the list of units.
     */
    public function viewAny(User $user): bool
    {
        return $user->can(PermissionEnum::UNIT_MANAGEMENT->value);
    }

    /**
     * Determine whether the user can create units.
     */
    public function create(User $user): bool
    {
        return $user->can(PermissionEnum::UNIT_CREATE->value);
    }

    /**
     * Determine whether the user can update the given unit.
     */
    public function update(User $user, Unit $unit): bool
    {
        return $user->can(PermissionEnum::UNIT_UPDATE->value);
    }

    /**
     * Determine whether the user can delete the given unit.
     */
    public function delete(User $user, Unit $unit): bool
    {
        return $user->can(PermissionEnum::UNIT_DELETE->value);
    }
}
