<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event dispatched when a user's profile is updated.
 *
 * Contains information about the user, changes made, and request context
 * for auditing and notification purposes.
 */
class ProfileUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param User $user The user who updated their profile
     * @param array<string, mixed> $changes The changes made to the profile
     * @param string $ipAddress IP address of the request
     * @param string $userAgent User agent of the request
     */
    public function __construct(
        public readonly User $user,
        public readonly array $changes,
        public readonly string $ipAddress,
        public readonly string $userAgent
    ) {}
}
