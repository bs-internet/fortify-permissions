<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\TwoFactorDisabled;
use App\Events\TwoFactorEnabled;
use App\Notifications\Profile\TwoFactorChangedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Listener for sending panel two-factor authentication notifications.
 *
 * Sends database notification when a panel user enables or disables
 * two-factor authentication.
 */
class SendTwoFactorNotification implements ShouldQueue
{
    /**
     * Handle the panel two-factor enabled event.
     *
     * @param TwoFactorEnabled $event The two-factor enabled event
     * @return void
     */
    public function handleEnabled(TwoFactorEnabled $event): void
    {
        $event->user->notify(
            new TwoFactorChangedNotification(
                true,
                $event->ipAddress,
                $event->userAgent
            )
        );
    }

    /**
     * Handle the panel two-factor disabled event.
     *
     * @param TwoFactorDisabled $event The two-factor disabled event
     * @return void
     */
    public function handleDisabled(TwoFactorDisabled $event): void
    {
        $event->user->notify(
            new TwoFactorChangedNotification(
                false,
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
            TwoFactorEnabled::class => 'handleEnabled',
            TwoFactorDisabled::class => 'handleDisabled',
        ];
    }
}
