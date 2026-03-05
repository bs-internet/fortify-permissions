<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\Activity;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/** Kullanıcı aktivitesi loglandığında tetiklenen event. */
class ActivityLogged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Yeni event örneği oluşturur.
     *
     * @param Activity $activity Oluşturulan aktivite kaydı
     */
    public function __construct(
        public readonly Activity $activity
    ) {}
}
