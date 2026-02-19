<?php

declare(strict_types=1);

namespace App\Services\Common;

use App\Events\ActivityLogged;
use App\Models\Activity;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class ActivityService
{
    /**
     * Log user activity with context.
     */
    public function log(
        User $user,
        string $type,
        string $description,
        array $log = [],
        ?string $ipAddress = null,
        ?string $userAgent = null,
    ): void {
        $request = request();

        $activity = Activity::create([
            'user_id'   => $user->id,
            'type'      => $type,
            'description' => $description,
            'log'       => $log,
            'ip_address' => $ipAddress ?? $request?->ip(),
            'user_agent' => $userAgent ?? $request?->userAgent(),
        ]);

        ActivityLogged::dispatch(
            $activity
        );
    }

    /**
     * Log system or anonymous activity.
     */
    public function logAnonymousActivity(
        string $type,
        string $description,
        array $log = [],
        ?string $ipAddress = null,
        ?string $userAgent = null,
    ): void {
        $request = request();

        Activity::create([
            'user_id'   => null,
            'type'      => $type,
            'description' => $description,
            'log'       => $log,
            'ip_address' => $ipAddress ?? $request?->ip(),
            'user_agent' => $userAgent ?? $request?->userAgent(),
        ]);
    }

    /**
     * Get paginated activities.
     */
    public function getPaginatedActivities(
        array $filters = [],
        int $perPage = 15
    ): LengthAwarePaginator {
        $query = Activity::query()->with('user');

        $sortField = $filters['sort_field'] ?? 'created_at';
        $sortDirection = $filters['sort_direction'] ?? 'desc';

        return $query
            ->orderBy($sortField, $sortDirection)
            ->paginate($perPage);
    }
}
