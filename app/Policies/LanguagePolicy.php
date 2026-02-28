<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Language;
use App\Models\User;
use App\Enums\PermissionEnum;

class LanguagePolicy
{
    /**
     * Determine whether the user can view the list of languages.
     */
    public function viewAny(User $user): bool
    {
        return $user->can(PermissionEnum::LANGUAGE_MANAGEMENT->value);
    }

    /**
     * Determine whether the user can create languages.
     */
    public function create(User $user): bool
    {
        return $user->can(PermissionEnum::LANGUAGE_CREATE->value);
    }

    /**
     * Determine whether the user can update the given language.
     */
    public function update(User $user, Language $language): bool
    {
        return $user->can(PermissionEnum::LANGUAGE_UPDATE->value);
    }

    /**
     * Determine whether the user can delete the given language.
     */
    public function delete(User $user, Language $language): bool
    {
        return $user->can(PermissionEnum::LANGUAGE_DELETE->value);
    }
}
