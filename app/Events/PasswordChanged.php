<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/** Kullanıcı şifresi değiştirildiğinde tetiklenen event. */
class PasswordChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Yeni event örneği oluşturur.
     *
     * @param User $user Şifresini değiştiren kullanıcı
     * @param string $ipAddress İstek IP adresi
     * @param string $userAgent İstek tarayıcı bilgisi
     */
    public function __construct(
        public readonly User $user,
        public readonly string $ipAddress,
        public readonly string $userAgent
    ) {}
}
