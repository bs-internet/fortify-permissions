<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\SettingsUpdated;
use App\Notifications\Settings\SettingsUpdatedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Listener for sending settings update notifications.
 */
class SendSettingsNotification implements ShouldQueue
{
    public int $tries = 3;

    public array $backoff = [10, 60, 300];

    public function failed(mixed $event, Throwable $exception): void
    {
        Log::error('SendSettingsNotification failed', [
            'event' => get_class($event),
            'error' => $exception->getMessage(),
        ]);
    }

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
