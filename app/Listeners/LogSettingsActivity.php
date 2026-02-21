<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\SettingsUpdated;
use App\Services\Common\ActivityService;

/**
 * Listener for logging system settings update activities.
 */
class LogSettingsActivity
{
    /**
     * Create the event listener.
     */
    public function __construct(
        private readonly ActivityService $activityService
    ) {}

    /**
     * Handle the settings updated event.
     */
    public function handleUpdated(SettingsUpdated $event): void
    {
        $this->activityService->log(
            user: $event->user,
            type: 'system',
            description: 'Sistem genel ayarlarını güncelledi',
            log: [
                'changes' => $event->changes,
            ],
            ipAddress: $event->ipAddress,
            userAgent: $event->userAgent,
        );
    }

    /**
     * Register the listeners for the subscriber.
     */
    public function subscribe($events): array
    {
        return [
            SettingsUpdated::class => 'handleUpdated',
        ];
    }
}
