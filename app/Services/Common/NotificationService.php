<?php

declare(strict_types=1);

namespace App\Services\Common;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

/**
 * Notification Service
 *
 * Service implementation for managing user notifications including
 * retrieval, reading, and archiving operations.
 */
class NotificationService
{
    /**
     * Get paginated notifications for the authenticated user.
     *
     * @param User $user
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPaginatedNotifications(User $user, int $perPage = 15): LengthAwarePaginator
    {
        return $user->notifications()
            ->orderBy('created_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Get paginated archived notifications for the authenticated user.
     *
     * @param User $user
     * @param int $perPage
     * @return LengthAwarePaginator
     */
    public function getPaginatedArchivedNotifications(User $user, int $perPage = 15): LengthAwarePaginator
    {
        return DB::table('archived_notifications')
            ->where('notifiable_type', get_class($user))
            ->where('notifiable_id', $user->id)
            ->orderBy('archived_at', 'desc')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Mark a notification as read.
     *
     * @param User $user
     * @param string $notificationId
     * @return bool
     */
    public function markAsRead(User $user, string $notificationId): bool
    {
        $notification = $user->notifications()->find($notificationId);

        if (!$notification) {
            return false;
        }

        if ($notification instanceof \Illuminate\Notifications\DatabaseNotification || method_exists($notification, 'markAsRead')) {
            $notification->markAsRead();

            return true;
        }

        return false;
    }

    /**
     * Mark all notifications as read for the authenticated user.
     *
     * @param User $user
     * @return int
     */
    public function markAllAsRead(User $user): int
    {
        return $user->unreadNotifications()->update(['read_at' => now()]);
    }

    /**
     * Get unread notification count for the authenticated user.
     *
     * @param User $user
     * @return int
     */
    public function getUnreadCount(User $user): int
    {
        return $user->unreadNotifications()->count();
    }

    /**
     * Delete all active notifications for a user.
     *
     * @param User $user
     * @return int
     */
    public function deleteAllForUser(User $user): int
    {
        return $user->notifications()->delete();
    }

}
