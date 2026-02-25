<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\LanguageCreated;
use App\Events\LanguageDeleted;
use App\Events\LanguageUpdated;
use App\Services\Common\ActivityService;

/**
 * Listener for logging language update activities.
 */
class LogLanguageActivity
{
    /**
     * Create the event listener.
     */
    public function __construct(
        private readonly ActivityService $activityService
    ) {}

    /**
     * Handle the language created event.
     */
    public function handleCreated(LanguageCreated $event): void
    {
        $this->activityService->log(
            user: $event->user,
            type: 'system',
            description: 'Yeni dil eklendi',
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
            description: 'Dil bilgisi güncellendi',
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
            description: 'Dil silindi',
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
