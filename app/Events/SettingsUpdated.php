<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event dispatched when system settings are updated.
 *
 * This event carries the user who performed the action,
 * the specific changes made to the settings, and request context
 * for audit logging and notification purposes.
 */
class SettingsUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param User $user The administrative user who updated settings
     * @param array<string, array{old: mixed, new: mixed}> $changes Key-value pairs of changed settings
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
