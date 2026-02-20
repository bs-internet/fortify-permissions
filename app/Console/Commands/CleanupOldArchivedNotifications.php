<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\ArchivedNotification;
use Illuminate\Console\Command;

class CleanupOldArchivedNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'archived-notifications:cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '120 günden eski arşivlenmiş bildirimleri soft delete eder.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $count = ArchivedNotification::whereNull('deleted_at')
            ->where('archived_at', '<', now()->subDays(120))
            ->update([
                'deleted_at' => now(),
                'updated_at' => now(),
            ]);

        $this->info("{$count} arşiv bildirimi soft delete edildi.");
    }
}
