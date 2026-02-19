<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Services\Common\ActivityService;
use App\Events\ProfileUpdated;

/**
 * Listener for logging panel profile update activities.
 *
 * Records profile update events with comprehensive audit information
 * for tracking and compliance purposes.
 */
class LogProfileActivity
{
    /**
     * Create the event listener.
     *
     * @param ActivityService $activityService Service for logging activities
     */
    public function __construct(
        private readonly ActivityService $activityService
    ) {}

    /**
     * Handle the panel profile updated event.
     *
     * @param ProfileUpdated $event The profile updated event
     * @return void
     */
    public function handleUpdated(ProfileUpdated $event): void
    {
        $this->activityService->log(
            user: $event->user,
            type: 'profile',
            description: 'Kullanıcı profil bilgilerini güncelledi',
            log: [
                'user_name' => $event->user->name,
                'changes' => $event->changes,
            ],
            ipAddress: $event->ipAddress,
            userAgent: $event->userAgent,
        );
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param \Illuminate\Events\Dispatcher $events The event dispatcher
     * @return array<class-string, string>
     */
    public function subscribe($events): array
    {
        return [
            ProfileUpdated::class => 'handleUpdated',
        ];
    }
}
