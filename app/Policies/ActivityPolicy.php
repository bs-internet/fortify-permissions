<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Activity;
use App\Models\User;

class ActivityPolicy
{
    /**
     * Determine whether the user can view the list of activities.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('activity.view');
    }
}
