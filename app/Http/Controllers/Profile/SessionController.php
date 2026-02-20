<?php

declare(strict_types=1);

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Rappasoft\LaravelAuthenticationLog\Models\AuthenticationLog;
use Torann\GeoIP\Facades\GeoIP;

class SessionController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $logs = AuthenticationLog::where('authenticatable_id', $user->id)
            ->where('authenticatable_type', get_class($user))
            ->orderByDesc('login_at')
            ->paginate(10)
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

        return Inertia::render('app/profile/Session', [
            'sessions' => $logs,
        ]);
    }

    public function destroy(Request $request, AuthenticationLog $log)
    {
        $this->authorizeLog($request, $log);

        $log->delete();

        return back()->with('success', 'Oturum başarıyla sonlandırıldı.');
    }

    public function destroyOther(Request $request)
    {
        $user = $request->user();

        AuthenticationLog::where('authenticatable_id', $user->id)
            ->where('authenticatable_type', get_class($user))
            ->where('ip_address', '!=', $request->ip())
            ->delete();

        return back()->with('success', 'Diğer tüm cihazlardaki oturumlar kapatıldı.');
    }

    protected function authorizeLog(Request $request, AuthenticationLog $log): void
    {
        if (
            $log->authenticatable_id !== $request->user()->id ||
            $log->authenticatable_type !== get_class($request->user())
        ) {
            abort(403);
        }
    }

    protected function resolveLocation(?string $ip): ?string
    {
        if (!$ip) {
            return null;
        }

        if (!class_exists(GeoIP::class)) {
            return null;
        }

        try {
            $record = GeoIP::getLocation($ip);

            if (!$record) {
                return null;
            }

            $city = $record->city ?? null;
            $country = $record->country ?? null;

            return collect([$city, $country])
                ->filter()
                ->implode(', ');
        } catch (\Throwable) {
            return null;
        }
    }
}
