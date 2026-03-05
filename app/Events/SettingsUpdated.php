<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/** Sistem ayarları güncellendiğinde tetiklenen event. */
class SettingsUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Yeni event örneği oluşturur.
     *
     * @param User $user Ayarları güncelleyen kullanıcı
     * @param array<string, array{old: mixed, new: mixed}> $changes Değiştirilen ayarlar
     * @param string $ipAddress İstek IP adresi
     * @param string $userAgent İstek tarayıcı bilgisi
     */
    public function __construct(
        public readonly User $user,
        public readonly array $changes,
        public readonly string $ipAddress,
        public readonly string $userAgent
    ) {}
}
