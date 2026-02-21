<?php

declare(strict_types=1);

namespace App\Services\Common;

use App\Models\ArchivedNotification;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

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
            ->through(fn ($n) => [
                'id' => $n->id,
                'type' => $n->type,
                'data' => $n->data,
                'read_at' => $n->read_at,
                'created_at_human' => $n->created_at->translatedFormat('d F Y H:i'),
            ])
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
        return ArchivedNotification::query()
            ->where('notifiable_type', $user->getMorphClass())
            ->where('notifiable_id', $user->id)
            ->orderBy('archived_at', 'desc')
            ->paginate($perPage)
            ->through(fn ($n) => [
                'id' => $n->id,
                'type' => $n->type,
                'data' => $n->data,
                'read_at' => $n->read_at,
                'archived_at_human' => $n->archived_at->translatedFormat('d F Y H:i'),
            ])
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
     * Archive a single notification.
     *
     * @param User $user
     * @param string $notificationId
     * @return bool
     */
    public function archive(User $user, string $notificationId): bool
    {
        $notification = $user->notifications()->find($notificationId);

        if (! $notification) {
            return false;
        }

        ArchivedNotification::create([
            'id' => $notification->id,
            'type' => $notification->type,
            'notifiable_id' => $notification->notifiable_id,
            'notifiable_type' => $notification->notifiable_type,
            'data' => $notification->data,
            'read_at' => $notification->read_at,
            'archived_at' => now(),
        ]);

        $notification->delete();

        return true;
    }

    /**
     * Archive all read notifications for a user.
     *
     * @param User $user
     * @return int
     */
    public function archiveAllRead(User $user): int
    {
        $notifications = $user->readNotifications()->get();

        foreach ($notifications as $notification) {
            ArchivedNotification::create([
                'id' => $notification->id,
                'type' => $notification->type,
                'notifiable_id' => $notification->notifiable_id,
                'notifiable_type' => $notification->notifiable_type,
                'data' => $notification->data,
                'read_at' => $notification->read_at,
                'archived_at' => now(),
            ]);

            $notification->delete();
        }

        return $notifications->count();
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
