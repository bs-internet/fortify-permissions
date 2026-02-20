<?php

declare(strict_types=1);

namespace App\Services\Profile;

use App\Events\PasswordChanged;
use App\Models\User;

/**
 * Password Service
 *
 * Service implementation for managing user passwords
 * including update operations with event dispatching.
 */
class PasswordService
{
    /**
     * Update the user's password.
     *
     * @param User $user The user whose password will be updated
     * @param string $password The new password
     * @param string $ipAddress IP address of the request
     * @param string $userAgent User agent of the request
     * @return User The updated user instance
     */
    public function update(User $user, string $password, string $ipAddress, string $userAgent): User
    {
        $user->update(['password' => $password]);
        PasswordChanged::dispatch($user, $ipAddress, $userAgent);
        return $user;
    }
}
