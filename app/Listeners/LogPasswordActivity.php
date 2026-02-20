<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Services\Common\ActivityService;
use App\Events\PasswordChanged;

/**
 * Listener for logging password change activities.
 *
 * Records password change events with comprehensive audit information
 * for security tracking and compliance purposes.
 */
class LogPasswordActivity
{
    /**
     * Create the event listener.
     *
     * @param ActivityService $activityService Service for logging activities
     */
    public function __construct(
        private readonly ActivityService $activityService
    ) {}

    /**
     * Handle the password changed event.
     *
     * @param PasswordChanged $event The password changed event
     * @return void
     */
    public function handle(PasswordChanged $event): void
    {
        $this->activityService->log(
            user: $event->user,
            type: 'profile',
            description: 'Kullanıcı şifresini değiştirdi',
            log: [
                'user_name' => $event->user->name,
                'user_email' => $event->user->email,
                'changed_at' => now()->toDateTimeString(),
            ],
            ipAddress: $event->ipAddress,
            userAgent: $event->userAgent,
        );
    }
}
