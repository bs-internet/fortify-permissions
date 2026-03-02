<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\CountryCreated;
use App\Events\CountryDeleted;
use App\Events\CountryUpdated;
use App\Services\Common\ActivityService;

/**
 * Listener for logging country activities.
 */
class LogCountryActivity
{
    /**
     * Create the event listener.
     */
    public function __construct(
        private readonly ActivityService $activityService
    ) {}

    /**
     * Handle the country created event.
     */
    public function handleCreated(CountryCreated $event): void
    {
        $this->activityService->log(
            user: $event->user,
            type: 'system',
            description: $event->user->name . ' yeni bir ülke ekledi',
            log: [
                'changes' => $event->changes,
            ],
            ipAddress: $event->ipAddress,
            userAgent: $event->userAgent,
        );
    }

    /**
     * Handle the country updated event.
     */
    public function handleUpdated(CountryUpdated $event): void
    {
        $this->activityService->log(
            user: $event->user,
            type: 'system',
            description: $event->user->name . ' ülke bilgisini güncelledi',
            log: [
                'changes' => $event->changes,
            ],
            ipAddress: $event->ipAddress,
            userAgent: $event->userAgent,
        );
    }

    /**
     * Handle the country deleted event.
     */
    public function handleDeleted(CountryDeleted $event): void
    {
        $this->activityService->log(
            user: $event->user,
            type: 'system',
            description: $event->user->name . ' bir ülke sildi',
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
            CountryCreated::class => 'handleCreated',
            CountryUpdated::class => 'handleUpdated',
            CountryDeleted::class => 'handleDeleted',
        ];
    }
}
