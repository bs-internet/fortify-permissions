<?php

declare(strict_types=1);

namespace App\Notifications\Settings;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class SettingsUpdatedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        private readonly array $changes
    ) {}

    /**
     * Get the notification's delivery channels.
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Sistem Ayarları Güncellendi',
            'message' => 'Genel sistem ayarları başarıyla güncellendi.',
            'updated_at' => now()->toIso8601String(),
        ];
    }
}
