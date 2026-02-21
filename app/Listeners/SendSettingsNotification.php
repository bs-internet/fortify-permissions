<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\SettingsUpdated;
use App\Notifications\Settings\SettingsUpdatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Listener for sending settings update notifications.
 */
class SendSettingsNotification implements ShouldQueue
{
    /**
     * Handle the settings updated event.
     */
    public function handle(SettingsUpdated $event): void
    {
        $event->user->notify(
            new SettingsUpdatedNotification($event->changes)
        );
    }

    /**
     * Register the listeners for the subscriber.
     */
    public function subscribe($events): array
    {
        return [
            SettingsUpdated::class => 'handle',
        ];
    }
}
