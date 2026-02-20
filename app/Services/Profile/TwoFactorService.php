<?php

declare(strict_types=1);

namespace App\Services\Profile;

use App\Events\TwoFactorDisabled;
use App\Events\TwoFactorEnabled;
use App\Models\User;

/**
 * Two Factor Service
 *
 * Service implementation for managing user two-factor authentication
 * including enable and disable operations with event dispatching.
 */
class TwoFactorService
{
    /**
     * Enable two-factor authentication for the user.
     *
     * @param User $user The user enabling two-factor authentication
     * @param string $ipAddress IP address of the request
     * @param string $userAgent User agent of the request
     * @return void
     */
    public function enable(User $user, string $ipAddress, string $userAgent): void
    {
        TwoFactorEnabled::dispatch($user, $ipAddress, $userAgent);
    }

    /**
     * Disable two-factor authentication for the user.
     *
     * @param User $user The user disabling two-factor authentication
     * @param string $ipAddress IP address of the request
     * @param string $userAgent User agent of the request
     * @return void
     */
    public function disable(User $user, string $ipAddress, string $userAgent): void
    {
        TwoFactorDisabled::dispatch($user, $ipAddress, $userAgent);
    }
}
