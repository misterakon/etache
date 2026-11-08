<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('ponctuelles:alertes')->dailyAt('05:00')->timezone('Africa/Abidjan')->withoutOverlapping();
Schedule::command('alertes:evaluer')->dailyAt('04:30')->timezone('Africa/Abidjan')->withoutOverlapping();
