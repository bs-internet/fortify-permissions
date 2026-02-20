<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event dispatched when a user's password is changed.
 *
 * Contains information about the user and request context
 * for auditing and notification purposes.
 */
class PasswordChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param User $user The user who changed their password
     * @param string $ipAddress IP address of the request
     * @param string $userAgent User agent of the request
     */
    public function __construct(
        public readonly User $user,
        public readonly string $ipAddress,
        public readonly string $userAgent
    ) {}
}
