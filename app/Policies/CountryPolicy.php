<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\DefinitionPermission;
use App\Models\Country;
use App\Models\User;

class CountryPolicy
{
    /**
     * Determine whether the user can view the list of countries.
     */
    public function viewAny(User $user): bool
    {
        return $user->can(DefinitionPermission::COUNTRY_MANAGEMENT->value);
    }

    /**
     * Determine whether the user can create countries.
     */
    public function create(User $user): bool
    {
        return $user->can(DefinitionPermission::COUNTRY_CREATE->value);
    }

    /**
     * Determine whether the user can update the given country.
     */
    public function update(User $user, Country $country): bool
    {
        return $user->can(DefinitionPermission::COUNTRY_UPDATE->value);
    }

    /**
     * Determine whether the user can delete the given country.
     */
    public function delete(User $user, Country $country): bool
    {
        return $user->can(DefinitionPermission::COUNTRY_DELETE->value);
    }
}
