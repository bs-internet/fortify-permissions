<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Event dispatched when user activity is logged.
 *
 * This event is fired whenever a user activity is successfully logged
 * in the panel system, providing context for potential listeners.
 * Note: This event carries full model objects as it's not queued.
 */
class ActivityLogged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param Activity $activity The activity log that was created
     */
    public function __construct(
        public readonly Activity $activity
    ) {}
}
