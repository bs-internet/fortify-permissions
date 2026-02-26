<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Language;
use App\Models\User;

class LanguagePolicy
{
    /**
     * Determine whether the user can view the list of languages.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('definition.management');
    }

    /**
     * Determine whether the user can create languages.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('definition.management');
    }

    /**
     * Determine whether the user can update the given language.
     */
    public function update(User $user, Language $language): bool
    {
        return $user->hasPermissionTo('definition.management');
    }

    /**
     * Determine whether the user can delete the given language.
     */
    public function delete(User $user, Language $language): bool
    {
        return $user->hasPermissionTo('definition.management');
    }
}
