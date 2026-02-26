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

        $ip = $ipAddress ?? $request?->ip() ?? 'unknown';
        $agent = $userAgent ?? $request?->userAgent() ?? 'unknown';

        $activity = Activity::create([
            'user_id' => $user->id,
            'type' => $type,
            'description' => $description,
            'log' => $log,
            'ip_address' => substr($ip, 0, 45),
            'user_agent' => mb_substr($agent, 0, 255),
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

        $ip = $ipAddress ?? $request?->ip() ?? 'unknown';
        $agent = $userAgent ?? $request?->userAgent() ?? 'unknown';

        Activity::create([
            'user_id' => null,
            'type' => $type,
            'description' => $description,
            'log' => $log,
            'ip_address' => substr($ip, 0, 45),
            'user_agent' => mb_substr($agent, 0, 255),
        ]);
    }

    /**
     * Get paginated activities.
     */
    public function getPaginatedActivities(
        array $filters = [],
        int $perPage = 25
    ): LengthAwarePaginator {
        $query = Activity::query()->with('user');

        if (!empty($filters['type']) && $filters['type'] !== 'all') {
            $query->where('type', $filters['type']);
        }

        $sortField = $filters['sort_field'] ?? 'created_at';
        $sortDirection = $filters['sort_direction'] ?? 'desc';

        return $query
            ->orderBy($sortField, $sortDirection)
            ->paginate($perPage)
            ->through(fn($activity) => [
                'id' => $activity->id,
                'type' => $activity->type,
                'description' => $activity->description,
                'ip_address' => $activity->ip_address,
                'date_human' => $activity->created_at->translatedFormat('d F Y H:i'),
                'log' => $activity->log,
            ]);
    }

    /**
     * Mevcut tüm aktivite tiplerini benzersiz olarak getirir.
     */
    public function getExistingTypes(): array
    {
        return Activity::query()
            ->distinct()
            ->pluck('type')
            ->toArray();
    }
}
