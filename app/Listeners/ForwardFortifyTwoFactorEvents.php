<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\TwoFactorDisabled;
use App\Events\TwoFactorEnabled;
use Illuminate\Support\Facades\Request;
use Laravel\Fortify\Events\TwoFactorAuthenticationDisabled;
use Laravel\Fortify\Events\TwoFactorAuthenticationEnabled;

/**
 * Listener for forwarding Fortify two-factor authentication events.
 *
 * Routes Fortify's generic two-factor events to user-type specific
 * application events for proper segregation and handling.
 */
class ForwardFortifyTwoFactorEvents
{
    /**
     * Handle the two-factor authentication enabled event.
     *
     * @param TwoFactorAuthenticationEnabled $event The Fortify event
     * @return void
     */
    public function handleEnabled(TwoFactorAuthenticationEnabled $event): void
    {
        $ipAddress = Request::ip() ?? '127.0.0.1';
        $userAgent = Request::userAgent() ?? 'unknown';

        event(new TwoFactorEnabled(
            $event->user,
            $ipAddress,
            $userAgent
        ));
    }

    /**
     * Handle the two-factor authentication disabled event.
     *
     * @param TwoFactorAuthenticationDisabled $event The Fortify event
     * @return void
     */
    public function handleDisabled(TwoFactorAuthenticationDisabled $event): void
    {
        $ipAddress = Request::ip() ?? '127.0.0.1';
        $userAgent = Request::userAgent() ?? 'unknown';

        event(new TwoFactorDisabled(
            $event->user,
            $ipAddress,
            $userAgent
        ));
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
            TwoFactorAuthenticationEnabled::class => 'handleEnabled',
            TwoFactorAuthenticationDisabled::class => 'handleDisabled',
        ];
    }
}
