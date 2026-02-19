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
    /**
     * Oturum listesini göster
     */
    public function index(Request $request)
    {
        $user = $request->user();

        // get() yerine paginate() kullanıyoruz
        $logs = AuthenticationLog::where('authenticatable_id', $user->id)
            ->where('authenticatable_type', get_class($user))
            ->orderBy('login_at', 'desc') // En yeni giriş en üstte
            ->paginate(10)
            ->through(function ($log) use ($request) {

                // GeoIP lokasyon işlemi
                $location = null;
                if ($log->ip_address) {
                    try {
                        $geo = GeoIP::getLocation($log->ip_address);

                        if ($geo) {
                            $location = collect([$geo->city, $geo->country])->filter()->implode(', ');
                        }
                    } catch (\Exception $e) { $location = 'Bilinmiyor'; }
                }

                return [
                    'id' => $log->id,
                    'ip_address' => $log->ip_address,
                    'location' => $location,
                    'user_agent' => $log->user_agent,
                    'is_current_device' => $log->ip_address === $request->ip(),
                    'login_at' => optional($log->login_at)->diffForHumans(), // Okunabilir format
                    'login_successful' => $log->login_successful,
                    // Vue tarafında beklenen eksik alanları ekliyoruz
                    'device_name' => $location ?? 'Bilinmeyen Cihaz',
                ];
            });

        return Inertia::render('app/profile/Session', [
            'sessions' => $logs,
        ]);
    }

    /**
     * Tek cihaz/oturum sil
     */
    public function destroy(Request $request, AuthenticationLog $log)
    {
        $this->authorizeLog($request, $log);
        $log->delete();
        return back()->with('success', 'Oturum başarıyla sonlandırıldı.');
    }

    /**
     * Diğer tüm cihazları sonlandır
     */
    public function destroyOther(Request $request)
    {
        $user = $request->user();

        AuthenticationLog::where('authenticatable_id', $user->id)
            ->where('authenticatable_type', get_class($user))
            ->where('ip_address', '!=', $request->ip())
            ->delete();

        return back()->with('success', 'Diğer tüm cihazlardaki oturumlar kapatıldı.');
    }

    /**
     * Güvenlik kontrolü
     */
    protected function authorizeLog(Request $request, AuthenticationLog $log)
    {
        if (
            $log->authenticatable_id !== $request->user()->id ||
            $log->authenticatable_type !== get_class($request->user())
        ) {
            abort(403);
        }
    }
}
