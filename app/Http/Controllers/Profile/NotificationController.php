<?php

declare(strict_types=1);

namespace App\Http\Controllers\Profile;

use App\Services\Common\NotificationService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Profile\MarkAsReadRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

/**
 * Controller for panel notification operations.
 *
 * Manages notification listing, reading, and archiving
 * for panel users.
 */
class NotificationController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @param NotificationService $notificationService Service for notification operations
     */
    public function __construct(
        private readonly NotificationService $notificationService
    ) {}

    /**
     * Display a listing of the user's notifications.
     *
     * @return View
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
     * Display a listing of the user's archived notifications.
     *
     * @return View
     */
    public function archived(): Response
    {
        $archivedNotifications = $this->notificationService->getPaginatedArchivedNotifications(Auth::user());

        return Inertia::render('app/profile/NotificationsArchived', [
            'archivedNotifications' => $archivedNotifications,
        ]);
    }

    /**
     * Mark a notification as read.
     *
     * @param MarkAsReadRequest $request
     * @return RedirectResponse
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
     * Mark all notifications as read.
     *
     * @return RedirectResponse
     */
    public function markAllAsRead(): RedirectResponse
    {
        $this->notificationService->markAllAsRead(Auth::user());
        return back()->with('success', 'Tüm bildirimler okundu olarak işaretlendi.');
    }
}
