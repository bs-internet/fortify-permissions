<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\RoleCreated;
use App\Events\RoleDeleted;
use App\Events\RoleUpdated;
use App\Services\Common\ActivityService;

/**
 * Listener for logging role update activities.
 */
class LogRoleActivity
{
    /**
     * Create the event listener.
     */
    public function __construct(
        private readonly ActivityService $activityService
    ) {}

    /**
     * Handle the role created event.
     */
    public function handleCreated(RoleCreated $event): void
    {
        $this->activityService->log(
            user: $event->user,
            type: 'system',
            description: 'Yeni kullanıcı birimi eklendi',
            log: [
                'changes' => $event->changes,
            ],
            ipAddress: $event->ipAddress,
            userAgent: $event->userAgent,
        );
    }

    /**
     * Handle the role updated event.
     */
    public function handleUpdated(RoleUpdated $event): void
    {
        $this->activityService->log(
            user: $event->user,
            type: 'system',
            description: 'Kullanıcı birimi bilgisi güncellendi',
            log: [
                'changes' => $event->changes,
            ],
            ipAddress: $event->ipAddress,
            userAgent: $event->userAgent,
        );
    }

    /**
     * Handle the role deleted event.
     */
    public function handleDeleted(RoleDeleted $event): void
    {
        $this->activityService->log(
            user: $event->user,
            type: 'system',
            description: 'Kullanıcı birimi silindi',
            log: [
                'changes' => $event->changes,
            ],
            ipAddress: $event->ipAddress,
            userAgent: $event->userAgent,
        );
    }

    /**
     * Handle the role deleted event.
     */
    public function subscribe($events): array
    {
        return [
            RoleCreated::class => 'handleCreated',
            RoleUpdated::class => 'handleUpdated',
            RoleDeleted::class => 'handleDeleted',
        ];
    }
}
