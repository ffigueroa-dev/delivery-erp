<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Job to reschedule pending orders daily at 23:00 in the configured timezone
Schedule::command('orders:reschedule-pending')
    ->dailyAt('23:00')
    ->timezone(config('app.timezone'))
    ->withoutOverlapping();
