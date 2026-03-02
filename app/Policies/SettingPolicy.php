<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Setting;
use App\Models\User;
use App\Enums\CorePermission;

class SettingPolicy
{
    /**
     * Determine whether the user can view the settings page.
     */
    public function viewAny(User $user): bool
    {
        return $user->can(CorePermission::SETTING_MANAGEMENT->value);
    }

    /**
     * Determine whether the user can update settings.
     */
    public function update(User $user): bool
    {
        return $user->can(CorePermission::SETTING_UPDATE->value);
    }
}
