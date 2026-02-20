<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\PasswordChanged;
use App\Notifications\Profile\PasswordUpdatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Listener for sending password change notifications.
 *
 * Sends both database and mail notifications when a user's
 * password is successfully changed.
 */
class SendPasswordNotification implements ShouldQueue
{
    /**
     * Handle the password changed event.
     *
     * @param PasswordChanged $event The password changed event
     * @return void
     */
    public function handle(PasswordChanged $event): void
    {
        $event->user->notify(
            new PasswordUpdatedNotification(
                $event->ipAddress,
                $event->userAgent
            )
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
            PasswordChanged::class => 'handle',
        ];
    }
}
