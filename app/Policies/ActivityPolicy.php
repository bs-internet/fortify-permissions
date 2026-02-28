<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Activity;
use App\Models\User;
use App\Enums\PermissionEnum;

class ActivityPolicy
{
    /**
     * Determine whether the user can view the list of activities.
     */
    public function viewAny(User $user): bool
    {
        return $user->can(PermissionEnum::ACTIVITY_VIEW->value);
    }
}
