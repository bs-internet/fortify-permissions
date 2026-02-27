<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Setting;
use App\Models\User;

class SettingPolicy
{
    /**
     * Determine whether the user can view the settings page.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('setting.management');
    }

    /**
     * Determine whether the user can update settings.
     */
    public function update(User $user): bool
    {
        return $user->can('setting.update');
    }
}
