<?php

declare(strict_types=1);

namespace App\Notifications\Users;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class UserUpdatedNotification extends Notification
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
        $changedFields = array_keys($this->changes);
        $fieldLabels = collect($changedFields)->map(fn (string $field) => match ($field) {
            'name' => 'ad',
            'email' => 'e-posta',
            'title' => 'ünvan',
            'status' => 'durum',
            'password' => 'şifre',
            'language_id' => 'dil',
            default => $field,
        })->implode(', ');

        return [
            'title' => 'Hesap Bilgileriniz Güncellendi',
            'message' => "Aşağıdaki bilgileriniz güncellendi: {$fieldLabels}.",
            'changes' => $this->changes,
            'created_at' => now()->toISOString(),
        ];
    }
}
