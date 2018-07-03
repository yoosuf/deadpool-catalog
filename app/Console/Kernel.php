<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        
        '\App\Console\Commands\UpdateExchanges',
        '\App\Console\Commands\ConvertCurrency',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('UpdateExchanges:updateExchanges')
        ->cron('0 */2 * * *');
        $schedule->command('ConvertCurrency:convertCurrency')
        ->cron('0 */8 * * *');
    }
}
