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

/* Oturum Açma Kayıtlarını Temizleme */
Schedule::command('authentication-log:clear')->daily();
