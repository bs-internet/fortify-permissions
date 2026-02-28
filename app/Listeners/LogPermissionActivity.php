<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\PermissionUpdated;
use App\Services\Common\ActivityService;

/**
 * Listener for logging permission update activities.
 */
class LogPermissionActivity
{
    /**
     * Create the event listener.
     */
    public function __construct(
        private readonly ActivityService $activityService
    ) {}

    /**
     * Handle the permission updated event.
     */
    public function handle(PermissionUpdated $event): void
    {
        $this->activityService->log(
            user: $event->user,
            type: 'system',
            description: 'Kullanım yetkisi bilgisi güncellendi',
            log: [
                'changes' => $event->changes,
            ],
            ipAddress: $event->ipAddress,
            userAgent: $event->userAgent,
        );
    }

    /**
     * Handle the permission deleted event.
     */
    public function subscribe($events): array
    {
        return [
            PermissionUpdated::class => 'handle',
        ];
    }
}
