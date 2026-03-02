<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\UserCreated;
use App\Events\UserDeleted;
use App\Events\UserUpdated;
use App\Services\Common\ActivityService;

/**
 * Listener for logging user update activities.
 */
class LogUserActivity
{
    /**
     * Create the event listener.
     */
    public function __construct(
        private readonly ActivityService $activityService
    ) {}

    /**
     * Handle the user created event.
     */
    public function handleCreated(UserCreated $event): void
    {
        $this->activityService->log(
            user: $event->user,
            type: 'system',
            description: $event->user->name . ' yeni bir kullanıcı ekledi',
            log: [
                'changes' => $event->changes,
            ],
            ipAddress: $event->ipAddress,
            userAgent: $event->userAgent,
        );
    }

    /**
     * Handle the user updated event.
     */
    public function handleUpdated(UserUpdated $event): void
    {
        $this->activityService->log(
            user: $event->user,
            type: 'system',
            description: $event->user->name . ' kullanıcı bilgisini güncelledi',
            log: [
                'changes' => $event->changes,
            ],
            ipAddress: $event->ipAddress,
            userAgent: $event->userAgent,
        );
    }

    /**
     * Handle the user deleted event.
     */
    public function handleDeleted(UserDeleted $event): void
    {
        $this->activityService->log(
            user: $event->user,
            type: 'system',
            description: $event->user->name . ' bir kullanıcı sildi',
            log: [
                'changes' => $event->changes,
            ],
            ipAddress: $event->ipAddress,
            userAgent: $event->userAgent,
        );
    }

    /**
     * Handle the user deleted event.
     */
    public function subscribe($events): array
    {
        return [
            UserCreated::class => 'handleCreated',
            UserUpdated::class => 'handleUpdated',
            UserDeleted::class => 'handleDeleted',
        ];
    }
}
