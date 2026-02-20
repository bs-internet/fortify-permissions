<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/* Activity Kayıtlarını Temizleme */
Schedule::command('activities:soft-delete-old')->daily();
Schedule::command('model:prune')->daily();

/* Bildirimleri Arşivleme ve Temizleme */
Schedule::command('notifications:archive-old')->daily();
Schedule::command('archived-notifications:cleanup')->daily();

/* Oturum Açma Kayıtlarını Temizleme */
Schedule::command('authentication-log:clear')->daily();
