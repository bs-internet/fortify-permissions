<?php

declare(strict_types=1);

namespace App\Services\Profile;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Rappasoft\LaravelAuthenticationLog\Models\AuthenticationLog;
use Torann\GeoIP\Facades\GeoIP;

class SessionService
{
    /**
     * Get paginated and formatted authentication logs for the given user.
     */
    public function getLogsForUser(Request $request): LengthAwarePaginator
    {
        $user = $request->user();

        return AuthenticationLog::where('authenticatable_id', $user->id)
            ->where('authenticatable_type', get_class($user))
            ->orderByDesc('login_at')
            ->paginate(config('otomasyon.pagination.per_page', 15))
            ->through(function ($log) use ($request) {
                return [
                    'id' => $log->id,
                    'ip_address' => $log->ip_address,
                    'location' => $this->resolveLocation($log->ip_address),
                    'user_agent' => $log->user_agent,
                    'is_current_device' => $log->ip_address === $request->ip(),
                    'login_at' => optional($log->login_at)?->diffForHumans(),
                    'login_successful' => $log->login_successful,
                ];
            });
    }

    /**
     * Delete a specific authentication log.
     */
    public function deleteLog(Request $request, AuthenticationLog $log): void
    {
        $this->authorizeLog($request, $log);
        $log->delete();
    }

    /**
     * Delete all authentication logs except the current device.
     */
    public function deleteOtherLogs(Request $request): void
    {
        $user = $request->user();

        AuthenticationLog::where('authenticatable_id', $user->id)
            ->where('authenticatable_type', get_class($user))
            ->where('ip_address', '!=', $request->ip())
            ->delete();
    }

    /**
     * Ensure the user owns the authentication log.
     */
    private function authorizeLog(Request $request, AuthenticationLog $log): void
    {
        if (
            $log->authenticatable_id !== $request->user()->id ||
            $log->authenticatable_type !== get_class($request->user())
        ) {
            abort(403);
        }
    }

    /**
     * Resolve the location based on IP address using GeoIP.
     */
    private function resolveLocation(?string $ip): ?string
    {
        if (!$ip || !class_exists(GeoIP::class)) {
            return null;
        }

        try {
            $record = GeoIP::getLocation($ip);

            if (!$record) {
                return null;
            }

            return collect([$record->city ?? null, $record->country ?? null])
                ->filter()
                ->implode(', ');
        } catch (\Throwable) {
            return null;
        }
    }
}
