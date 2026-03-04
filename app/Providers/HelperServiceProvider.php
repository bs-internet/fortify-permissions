<?php

namespace App\Providers;

use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class HelperServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $helperDir = app_path('Helpers');

        if (!is_dir($helperDir)) {
            return;
        }

        $pattern = $helperDir . '/*_helpers.php';
        $files = File::glob($pattern) ?: [];

        foreach ($files as $file) {
            require_once $file;
        }
    }
}
