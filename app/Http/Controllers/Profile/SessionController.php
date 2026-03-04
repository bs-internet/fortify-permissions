<?php

declare(strict_types=1);

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Services\Profile\SessionService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Rappasoft\LaravelAuthenticationLog\Models\AuthenticationLog;

class SessionController extends Controller
{
    public function __construct(private SessionService $sessionService)
    {
    }
    public function index(Request $request)
    {
        $logs = $this->sessionService->getLogsForUser($request);

        return Inertia::render('app/profile/Session', [
            'sessions' => $logs,
        ]);
    }

    public function destroy(Request $request, AuthenticationLog $log)
    {
        $this->sessionService->deleteLog($request, $log);

        return back()->with('success', 'Oturum başarıyla sonlandırıldı.');
    }

    public function destroyOther(Request $request)
    {
        $this->sessionService->deleteOtherLogs($request);

        return back()->with('success', 'Diğer tüm cihazlardaki oturumlar kapatıldı.');
    }

}
