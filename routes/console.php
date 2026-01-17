<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Schedule network service commands
Schedule::command('ipam:cleanup --force')->daily()->at('00:00');
Schedule::command('radius:sync-users --force')->everyFiveMinutes();
Schedule::command('mikrotik:sync-sessions')->everyMinute();
Schedule::command('mikrotik:health-check')->everyFifteenMinutes();

// Schedule OLT service commands
Schedule::command('olt:health-check')->everyFifteenMinutes();
Schedule::command('olt:sync-onus')->hourly();
Schedule::command('olt:backup')->daily()->at('02:00');
