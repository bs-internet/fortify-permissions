<?php

declare(strict_types=1);

namespace App\Notifications\Users;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class UserCreatedNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @param array<string, mixed> $changes
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
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Hesabınız Oluşturuldu',
            'message' => 'Sistem yöneticisi tarafından hesabınız oluşturuldu.',
            'created_at' => now()->toISOString(),
        ];
    }
}
