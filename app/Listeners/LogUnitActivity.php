<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\UnitCreated;
use App\Events\UnitDeleted;
use App\Events\UnitUpdated;
use App\Services\Common\ActivityService;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Listener for logging unit update activities.
 */
class LogUnitActivity implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct(
        private readonly ActivityService $activityService
    ) {
    }

    /**
     * Handle the unit created event.
     */
    public function handleCreated(UnitCreated $event): void
    {
        $this->activityService->log(
            user: $event->user,
            type: 'system',
            description: $event->user->name . ' yeni bir birim ekledi',
            log: [
                'changes' => $event->changes,
            ],
            ipAddress: $event->ipAddress,
            userAgent: $event->userAgent,
        );
    }

    /**
     * Handle the unit updated event.
     */
    public function handleUpdated(UnitUpdated $event): void
    {
        $this->activityService->log(
            user: $event->user,
            type: 'system',
            description: $event->user->name . ' birim bilgisini güncelledi',
            log: [
                'changes' => $event->changes,
            ],
            ipAddress: $event->ipAddress,
            userAgent: $event->userAgent,
        );
    }

    /**
     * Handle the unit deleted event.
     */
    public function handleDeleted(UnitDeleted $event): void
    {
        $this->activityService->log(
            user: $event->user,
            type: 'system',
            description: $event->user->name . ' bir birim sildi',
            log: [
                'changes' => $event->changes,
            ],
            ipAddress: $event->ipAddress,
            userAgent: $event->userAgent,
        );
    }

    /**
     * Handle the unit deleted event.
     */
    public function subscribe($events): array
    {
        return [
            UnitCreated::class => 'handleCreated',
            UnitUpdated::class => 'handleUpdated',
            UnitDeleted::class => 'handleDeleted',
        ];
    }
}
