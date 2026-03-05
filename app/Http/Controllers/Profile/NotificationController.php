<?php

declare(strict_types=1);

namespace App\Http\Controllers\Profile;

use App\Services\Common\NotificationService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\ArchiveNotificationRequest;
use App\Http\Requests\Profile\MarkAsReadRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class NotificationController extends Controller
{
    public function __construct(
        protected NotificationService $notificationService
    ) {}

    /**
     * Bildirim listeleme sayfası.
     */
    public function index(): Response
    {
        $notifications = $this->notificationService->getPaginatedNotifications(Auth::user());
        $unreadCount = $this->notificationService->getUnreadCount(Auth::user());

        return Inertia::render('app/profile/Notifications', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
        ]);
    }

    /**
     * Arşivlenmiş bildirim listeleme sayfası.
     */
    public function archived(): Response
    {
        $archivedNotifications = $this->notificationService->getPaginatedArchivedNotifications(Auth::user());

        return Inertia::render('app/profile/NotificationsArchived', [
            'archivedNotifications' => $archivedNotifications,
        ]);
    }

    /**
     * Bildirimi okundu olarak işaretle.
     */
    public function markAsRead(MarkAsReadRequest $request): RedirectResponse
    {
        $this->notificationService->markAsRead(
            Auth::user(),
            $request->validated('notification_id')
        );

        return back();
    }

    /**
     * Tüm bildirimleri okundu olarak işaretle.
     */
    public function markAllAsRead(): RedirectResponse
    {
        $this->notificationService->markAllAsRead(Auth::user());

        return back()->with('success', 'Tüm bildirimler okundu olarak işaretlendi.');
    }

    /**
     * Bildirimi arşivle.
     */
    public function archive(ArchiveNotificationRequest $request): RedirectResponse
    {
        $this->notificationService->archive(
            Auth::user(),
            $request->validated('notification_id')
        );

        return back()->with('success', 'Bildirim arşivlendi.');
    }

    /**
     * Okunan tüm bildirimleri arşivle.
     */
    public function archiveAllRead(): RedirectResponse
    {
        $count = $this->notificationService->archiveAllRead(Auth::user());

        return back()->with('success', "{$count} bildirim arşivlendi.");
    }
}
