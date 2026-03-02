<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Currency;
use App\Models\User;
use App\Enums\DefinitionPermission;

class CurrencyPolicy
{
    /**
     * Determine whether the user can view the list of currencies.
     */
    public function viewAny(User $user): bool
    {
        return $user->can(DefinitionPermission::CURRENCY_MANAGEMENT->value);
    }

    /**
     * Determine whether the user can create currencies.
     */
    public function create(User $user): bool
    {
        return $user->can(DefinitionPermission::CURRENCY_CREATE->value);
    }

    /**
     * Determine whether the user can update the given currency.
     */
    public function update(User $user, Currency $currency): bool
    {
        return $user->can(DefinitionPermission::CURRENCY_UPDATE->value);
    }

    /**
     * Determine whether the user can delete the given currency.
     */
    public function delete(User $user, Currency $currency): bool
    {
        return $user->can(DefinitionPermission::CURRENCY_DELETE->value);
    }
}
