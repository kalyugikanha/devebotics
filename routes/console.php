<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schedule;

/*
|--------------------------------------------------------------------------
| Scheduled tasks
|--------------------------------------------------------------------------
|
| Requires one cron entry on the server:
|   * * * * * cd /path/to/app && php artisan schedule:run >> /dev/null 2>&1
|
*/

// Flips scheduled blog posts to "published" once their publication time has passed.
Schedule::command('softec:publish-scheduled')->everyMinute()->withoutOverlapping();

// Heartbeat read by Admin › System health to confirm the scheduler is running.
Schedule::call(fn () => Cache::put('scheduler.heartbeat', now()->toIso8601String(), 600))
    ->everyMinute()
    ->name('scheduler-heartbeat');

Schedule::command('softec:prune-data')->dailyAt('03:15')->withoutOverlapping()->onOneServer();

Schedule::command('queue:prune-failed', ['--hours' => 24 * 30])->weekly();

if (filled(config('backup.backup.name')) && filled(config('backup.backup.destination.disks'))) {
    Schedule::command('backup:clean')->dailyAt('01:30')->onOneServer();
    Schedule::command('backup:run', ['--only-db'])->dailyAt('02:00')->onOneServer();
    // Weekly full backup: database + uploaded media and private attachments.
    Schedule::command('backup:run')->weeklyOn(0, '02:30')->onOneServer();
}
