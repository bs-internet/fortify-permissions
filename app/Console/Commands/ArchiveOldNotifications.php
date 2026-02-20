<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\ArchivedNotification;
use App\Models\Notification;
use Illuminate\Console\Command;

class ArchiveOldNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:archive-old';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '30 günden eski okunmuş bildirimleri arşive taşır.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $notifications = Notification::query()
            ->whereNotNull('read_at')
            ->where('read_at', '<', now()->subDays(60))
            ->get();

        $count = $notifications->count();

        if ($count === 0) {
            $this->info('Arşivlenecek bildirim bulunamadı.');

            return;
        }

        /** @var Notification $notification */
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

        $this->info("{$count} bildirim arşive taşındı.");
    }
}
