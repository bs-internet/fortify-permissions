<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\TaxCreated;
use App\Events\TaxDeleted;
use App\Events\TaxUpdated;
use App\Models\Activity;
use App\Services\Common\ActivityService;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Listener for logging tax activities.
 */
class LogTaxActivity implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct(
        private readonly ActivityService $activityService
    ) {
    }

    /**
     * Handle the tax created event.
     */
    public function handleCreated(TaxCreated $event): void
    {
        $this->activityService->log(
            user: $event->user,
            type: 'system',
            description: $event->user->name . ' yeni bir vergi oranı ekledi',
            log: [
                'changes' => $event->changes,
            ],
            ipAddress: $event->ipAddress,
            userAgent: $event->userAgent,
        );
    }

    /**
     * Handle the tax updated event.
     */
    public function handleUpdated(TaxUpdated $event): void
    {
        $this->activityService->log(
            user: $event->user,
            type: 'system',
            description: $event->user->name . ' vergi oranı bilgisini güncelledi',
            log: [
                'changes' => $event->changes,
            ],
            ipAddress: $event->ipAddress,
            userAgent: $event->userAgent,
        );
    }

    /**
     * Handle the tax deleted event.
     */
    public function handleDeleted(TaxDeleted $event): void
    {
        $this->activityService->log(
            user: $event->user,
            type: 'system',
            description: $event->user->name . ' bir vergi oranı sildi',
            log: [
                'changes' => $event->changes,
            ],
            ipAddress: $event->ipAddress,
            userAgent: $event->userAgent,
        );
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @return array<class-string, string>
     */
    public function subscribe($events): array
    {
        return [
            TaxCreated::class => 'handleCreated',
            TaxUpdated::class => 'handleUpdated',
            TaxDeleted::class => 'handleDeleted',
        ];
    }
}
