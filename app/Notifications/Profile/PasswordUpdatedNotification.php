<?php

declare(strict_types=1);

namespace App\Notifications\Profile;

use App\Mail\Profile\PasswordUpdatedMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

/**
 * Notification for password updates.
 *
 * Sends both database and mail notifications when a user's
 * password is changed for security tracking.
 */
class PasswordUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @param string $ipAddress IP address from which the password was changed
     * @param string $userAgent User agent from which the password was changed
     */
    public function __construct(
        private readonly string $ipAddress,
        private readonly string $userAgent
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable The notifiable entity
     * @return array<int, string>
     */
    public function via(mixed $notifiable): array
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable The notifiable entity
     * @return PasswordUpdatedMail
     */
    public function toMail(mixed $notifiable): PasswordUpdatedMail
    {
        return new PasswordUpdatedMail(
            $notifiable,
            $this->ipAddress,
            $this->userAgent
        );
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable The notifiable entity
     * @return array<string, mixed>
     */
    public function toArray(mixed $notifiable): array
    {
        return [
            'title' => 'Şifreniz Değiştirildi',
            'message' => 'Hesabınızın şifresi başarıyla değiştirildi.',
            'type' => 'security',
            'ip_address' => $this->ipAddress,
            'user_agent' => $this->userAgent,
            'changed_at' => now()->toDateTimeString(),
        ];
    }
}
