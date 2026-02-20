<?php

namespace App\Console\Commands;

use App\Models\Activity;
use Illuminate\Console\Command;

class SoftDeleteOldActivities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'activities:soft-delete-old';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '6 aydan eski aktivite kayıtlarını siler.';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        Activity::whereNull('deleted_at')
            ->where('created_at', '<', now()->subMonths(6))
            ->update([
                'deleted_at' => now(),
                'updated_at' => now(),
            ]);
    }
}
