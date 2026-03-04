<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\LanguageCreated;
use App\Events\LanguageDeleted;
use App\Events\LanguageUpdated;
use App\Services\Common\ActivityService;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Listener for logging language update activities.
 */
class LogLanguageActivity implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct(
        private readonly ActivityService $activityService
    ) {
    }

    /**
     * Handle the language created event.
     */
    public function handleCreated(LanguageCreated $event): void
    {
        $this->activityService->log(
            user: $event->user,
            type: 'system',
            description: $event->user->name . ' yeni bir dil ekledi',
            log: [
                'changes' => $event->changes,
            ],
            ipAddress: $event->ipAddress,
            userAgent: $event->userAgent,
        );
    }

    /**
     * Handle the language updated event.
     */
    public function handleUpdated(LanguageUpdated $event): void
    {
        $this->activityService->log(
            user: $event->user,
            type: 'system',
            description: $event->user->name . ' dil bilgisini güncelledi',
            log: [
                'changes' => $event->changes,
            ],
            ipAddress: $event->ipAddress,
            userAgent: $event->userAgent,
        );
    }

    /**
     * Handle the language deleted event.
     */
    public function handleDeleted(LanguageDeleted $event): void
    {
        $this->activityService->log(
            user: $event->user,
            type: 'system',
            description: $event->user->name . ' bir dil sildi',
            log: [
                'changes' => $event->changes,
            ],
            ipAddress: $event->ipAddress,
            userAgent: $event->userAgent,
        );
    }

    /**
     * Handle the language deleted event.
     */
    public function subscribe($events): array
    {
        return [
            LanguageCreated::class => 'handleCreated',
            LanguageUpdated::class => 'handleUpdated',
            LanguageDeleted::class => 'handleDeleted',
        ];
    }
}
