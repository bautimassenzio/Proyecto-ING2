<?php

namespace App\Console;

use App\Console\Commands\CleanUpPendingReservations;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;


class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     * 
     * 
     */

     protected $commands = [
       
        CleanUpPendingReservations::class, // aquí agregas los comandos que quieres que estén disponibles
    ];
    protected function schedule(Schedule $schedule)
{
     $schedule->command('reservas:cleanup-pending')->everyMinute(); // o daily(), hourly(), etc.
}


    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }

    
}
