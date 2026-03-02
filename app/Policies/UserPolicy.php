<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;
use App\Enums\CorePermission;

class UserPolicy
{
    /**
     * Determine whether the user can view the list of users.
     */
    public function viewAny(User $user): bool
    {
        return $user->can(CorePermission::USER_MANAGEMENT->value);
    }

    /**
     * Determine whether the user can create users.
     */
    public function create(User $user): bool
    {
        return $user->can(CorePermission::USER_CREATE->value);
    }

    /**
     * Determine whether the user can update the given user.
     */
    public function update(User $user, User $model): bool
    {
        if (!$user->can(CorePermission::USER_UPDATE->value)) {
            return false;
        }

        // Kullanıcı kendini düzenleyemez (profil sayfasından yapmalı)
        return $user->id !== $model->id;
    }

    /**
     * Determine whether the user can delete the given user.
     */
    public function delete(User $user, User $model): bool
    {
        if (!$user->can(CorePermission::USER_DELETE->value)) {
            return false;
        }

        // Kullanıcı kendini silemez
        return $user->id !== $model->id;
    }
}
