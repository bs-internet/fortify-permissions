<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\CurrencyCreated;
use App\Events\CurrencyDeleted;
use App\Events\CurrencyUpdated;
use App\Services\Common\ActivityService;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Listener for logging currency update activities.
 */
class LogCurrencyActivity implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct(
        private readonly ActivityService $activityService
    ) {
    }

    /**
     * Handle the currency created event.
     */
    public function handleCreated(CurrencyCreated $event): void
    {
        $this->activityService->log(
            user: $event->user,
            type: 'system',
            description: $event->user->name . ' yeni bir para birimi ekledi',
            log: [
                'changes' => $event->changes,
            ],
            ipAddress: $event->ipAddress,
            userAgent: $event->userAgent,
        );
    }

    /**
     * Handle the currency updated event.
     */
    public function handleUpdated(CurrencyUpdated $event): void
    {
        $this->activityService->log(
            user: $event->user,
            type: 'system',
            description: $event->user->name . ' para birimi bilgisini güncelledi',
            log: [
                'changes' => $event->changes,
            ],
            ipAddress: $event->ipAddress,
            userAgent: $event->userAgent,
        );
    }

    /**
     * Handle the currency deleted event.
     */
    public function handleDeleted(CurrencyDeleted $event): void
    {
        $this->activityService->log(
            user: $event->user,
            type: 'system',
            description: $event->user->name . ' bir para birimi sildi',
            log: [
                'changes' => $event->changes,
            ],
            ipAddress: $event->ipAddress,
            userAgent: $event->userAgent,
        );
    }

    /**
     * Handle the currency deleted event.
     */
    public function subscribe($events): array
    {
        return [
            CurrencyCreated::class => 'handleCreated',
            CurrencyUpdated::class => 'handleUpdated',
            CurrencyDeleted::class => 'handleDeleted',
        ];
    }
}
