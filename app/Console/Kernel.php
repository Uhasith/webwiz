<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;


class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */

     protected $commands = [
        \App\Console\Commands\SensorCommand::class,
        \App\Console\Commands\FifteenMinuteCommand::class,
        \App\Console\Commands\HourlyCommand::class,
        \App\Console\Commands\DailyCommand::class,
        \App\Console\Commands\WeeklyCommand::class,
        \App\Console\Commands\MonthlyCommand::class,
        \App\Console\Commands\AnnuallyCommand::class,
    ];


    protected function schedule(Schedule $schedule): void
    {
        Log::info('Schedule is starting.');
        $schedule->command('app:five-minute-command')->everyFiveMinutes();
        $schedule->command('app:fifteen-minute-command')->everyFifteenMinutes();
        $schedule->command('app:hourly-command')->hourlyAt($minutes = 0);
        $schedule->command('app:daily-command')->dailyAt('00:00');
        $schedule->command('app:weekly-command')->weekly()->at('00:00');
        $schedule->command('app:monthly-command')->monthly()->at('00:00');
        $schedule->command('app:annually-command')->yearly()->at('00:00');
        $schedule->command('app:send-notifications-command')->everyFiveMinutes();

    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
