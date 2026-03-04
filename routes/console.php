<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/* Activity Kayıtlarını Temizleme */
Schedule::command('activities:soft-delete-old')->dailyAt('01:00');
Schedule::command('model:prune')->dailyAt('02:00');

/* Bildirimleri Arşivleme ve Temizleme */
Schedule::command('notifications:archive-old')->dailyAt('03:00');
Schedule::command('archived-notifications:cleanup')->dailyAt('04:00');

/* Oturum Açma Kayıtlarını Temizleme */
Schedule::command('authentication-log:clear')->dailyAt('05:00');
