<?php

namespace App\Console;

use App\Models\DateModel;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->call(function () {
            // Obtener todos los semestres que aún no están cerrados
            $semesters = DateModel::where('is_closed', false)->get();

            // Verificar la fecha de cierre de cada semestre
            foreach ($semesters as $semester) {
                if ($semester->closing_date && $semester->closing_date <= now()) {
                    // Cambiar el estado de is_closed a true
                    $semester->update(['is_closed' => true]);
                }
            }
        })->timezone('America/Lima')
            ->dailyAt('23:59');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
