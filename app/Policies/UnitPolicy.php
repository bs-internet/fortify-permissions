<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Unit;
use App\Models\User;

class UnitPolicy
{
    /**
     * Determine whether the user can view the list of units.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('unit.management');
    }

    /**
     * Determine whether the user can create units.
     */
    public function create(User $user): bool
    {
        return $user->can('unit.create');
    }

    /**
     * Determine whether the user can update the given unit.
     */
    public function update(User $user, Unit $unit): bool
    {
        return $user->can('unit.update');
    }

    /**
     * Determine whether the user can delete the given unit.
     */
    public function delete(User $user, Unit $unit): bool
    {
        return $user->can('unit.delete');
    }
}
