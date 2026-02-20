<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Services\Common\ActivityService;
use App\Events\TwoFactorDisabled;
use App\Events\TwoFactorEnabled;

/**
 * Listener for logging two-factor authentication activities.
 *
 * Records two-factor enable/disable events with comprehensive audit information
 * for security tracking and compliance purposes.
 */
class LogTwoFactorActivity
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
     * Handle the two-factor enabled event.
     *
     * @param TwoFactorEnabled $event The two-factor enabled event
     * @return void
     */
    public function handleEnabled(TwoFactorEnabled $event): void
    {
        $this->activityService->log(
            user: $event->user,
            type: 'profile',
            description: 'Kullanıcı iki faktörlü doğrulamayı etkinleştirdi',
            log: [
                'user_name' => $event->user->name,
                'user_email' => $event->user->email,
                'enabled_at' => now()->toDateTimeString(),
            ],
            ipAddress: $event->ipAddress,
            userAgent: $event->userAgent,
        );
    }

    /**
     * Handle the two-factor disabled event.
     *
     * @param TwoFactorDisabled $event The two-factor disabled event
     * @return void
     */
    public function handleDisabled(TwoFactorDisabled $event): void
    {
        $this->activityService->log(
            user: $event->user,
            type: 'profile',
            description: 'Kullanıcı iki faktörlü doğrulamayı devre dışı bıraktı',
            log: [
                'user_name' => $event->user->name,
                'user_email' => $event->user->email,
                'disabled_at' => now()->toDateTimeString(),
            ],
            ipAddress: $event->ipAddress,
            userAgent: $event->userAgent,
        );
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param \Illuminate\Events\Dispatcher $events The event dispatcher
     * @return array<class-string, string>
     */
    public function subscribe($events): array
    {
        return [
            TwoFactorEnabled::class => 'handleEnabled',
            TwoFactorDisabled::class => 'handleDisabled',
        ];
    }
}
