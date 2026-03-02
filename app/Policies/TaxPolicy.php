<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\DefinitionPermission;
use App\Models\Tax;
use App\Models\User;

class TaxPolicy
{
    /**
     * Determine whether the user can view the list of taxes.
     */
    public function viewAny(User $user): bool
    {
        return $user->can(DefinitionPermission::TAX_MANAGEMENT->value);
    }

    /**
     * Determine whether the user can create taxes.
     */
    public function create(User $user): bool
    {
        return $user->can(DefinitionPermission::TAX_CREATE->value);
    }

    /**
     * Determine whether the user can update the given tax.
     */
    public function update(User $user, Tax $tax): bool
    {
        return $user->can(DefinitionPermission::TAX_UPDATE->value);
    }

    /**
     * Determine whether the user can delete the given tax.
     */
    public function delete(User $user, Tax $tax): bool
    {
        return $user->can(DefinitionPermission::TAX_DELETE->value);
    }
}
