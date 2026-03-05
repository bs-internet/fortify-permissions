<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\UserUpdated;
use App\Events\UserCreated;
use App\Notifications\Users\UserUpdatedNotification;
use App\Notifications\Users\UserCreatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Listener for sending user update notifications.
 *
 * Sends database notification when a panel user's user is updated.
 */
class SendUserNotification implements ShouldQueue
{
    public int $tries = 3;

    public array $backoff = [10, 60, 300];

    public function failed(mixed $event, Throwable $exception): void
    {
        Log::error('SendUserNotification failed', [
            'event' => get_class($event),
            'error' => $exception->getMessage(),
        ]);
    }

    /**
     * Handle the panel user updated event.
     *
     * @param UserUpdated $event The user updated event
     * @return void
     */
    public function handleUpdated(UserUpdated $event): void
    {
        $event->user->notify(
            new UserUpdatedNotification($event->changes)
        );
    }
    /**
     * Handle the panel user created event.
     *
     * @param UserCreated $event The user created event
     * @return void
     */
    public function handleCreated(UserCreated $event): void
    {
        $event->user->notify(
            new UserCreatedNotification(['created' => true])
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
            UserUpdated::class => 'handleUpdated',
            UserCreated::class => 'handleCreated',
        ];
    }
}
