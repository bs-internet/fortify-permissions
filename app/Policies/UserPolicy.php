<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can view the list of users.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('user.management');
    }

    /**
     * Determine whether the user can create users.
     */
    public function create(User $user): bool
    {
        return $user->can('user.create');
    }

    /**
     * Determine whether the user can update the given user.
     */
    public function update(User $user, User $model): bool
    {
        if (!$user->can('user.update')) {
            return false;
        }

        // Kullanıcı kendini düzenleyemez (profil sayfasından yapmalı)
        if ($user->id === $model->id) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can delete the given user.
     */
    public function delete(User $user, User $model): bool
    {
        if (!$user->can('user.delete')) {
            return false;
        }

        // Kullanıcı kendini silemez
        if ($user->id === $model->id) {
            return false;
        }

        return true;
    }
}
