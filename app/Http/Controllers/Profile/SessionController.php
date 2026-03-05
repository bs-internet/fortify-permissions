<?php

declare(strict_types=1);

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Services\Profile\SessionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Rappasoft\LaravelAuthenticationLog\Models\AuthenticationLog;

/**
 * Kullanıcı oturum yönetimi controller'ı.
 */
class SessionController extends Controller
{
    public function __construct(private SessionService $sessionService) {}

    /**
     * Oturum listesi sayfası.
     */
    public function index(Request $request): Response
    {
        $logs = $this->sessionService->getLogsForUser($request);

        return Inertia::render('app/profile/Session', [
            'sessions' => $logs,
        ]);
    }

    /**
     * Belirtilen oturumu sonlandır.
     */
    public function destroy(Request $request, AuthenticationLog $log): RedirectResponse
    {
        $this->sessionService->deleteLog($request, $log);

        return back()->with('success', 'Oturum başarıyla sonlandırıldı.');
    }

    /**
     * Diğer tüm oturumları sonlandır.
     */
    public function destroyOther(Request $request): RedirectResponse
    {
        $this->sessionService->deleteOtherLogs($request);

        return back()->with('success', 'Diğer tüm cihazlardaki oturumlar kapatıldı.');
    }
}
