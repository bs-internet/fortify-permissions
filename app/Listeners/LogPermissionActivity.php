<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\PermissionUpdated;
use App\Services\Common\ActivityService;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Listener for logging permission update activities.
 */
class LogPermissionActivity implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct(
        private readonly ActivityService $activityService
    ) {
    }

    /**
     * Handle the permission updated event.
     */
    public function handle(PermissionUpdated $event): void
    {
        $this->activityService->log(
            user: $event->user,
            type: 'system',
            description: $event->user->name . ' yetki bilgisini güncelledi',
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
