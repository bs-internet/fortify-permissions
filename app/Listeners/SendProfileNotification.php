<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\ProfileUpdated;
use App\Notifications\Profile\ProfileUpdatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Listener for sending profile update notifications.
 *
 * Sends database notification when a panel user's profile is updated.
 */
class SendProfileNotification implements ShouldQueue
{
    /**
     * Handle the panel profile updated event.
     *
     * @param ProfileUpdated $event The profile updated event
     * @return void
     */
    public function handle(ProfileUpdated $event): void
    {
        $event->user->notify(
            new ProfileUpdatedNotification($event->changes)
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
            ProfileUpdated::class => 'handle',
        ];
    }
}
