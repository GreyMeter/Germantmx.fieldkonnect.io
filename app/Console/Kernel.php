<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\EveryNight;
use App\Console\Commands\AutoPunchOut;
use App\Console\Commands\AddressUpdate;
use App\Console\Commands\MoveStorageToS3;
use App\Models\User;

class Kernel extends ConsoleKernel
{

    protected $commands = [
        \App\Console\Commands\MoveStorageToS3::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //$schedule->command(EveryNight::class, ['--force'])->dailyAt('13:00');
        $schedule->command(EveryNight::class)->timezone('Asia/Kolkata')->dailyAt('23:00');
        $schedule->command(AutoPunchOut::class)->timezone('Asia/Kolkata')->dailyAt('22:30');
        $schedule->command(AddressUpdate::class)->timezone('Asia/Kolkata')->everyFourHours();
        $schedule->command('backup:run')->monthlyOn(1, '00:00');

        // $schedule->call(function () {
        //     $users = User::where('active', 'Y')->get();
        //     foreach ($users as $user) {
        //         $pinchinnotify = collect([
        //             'title' => 'Punch In Now',
        //             'body' =>  $user->name . 'Please Punch In Now.'
        //         ]);
        //         sendNotification($user->id, $pinchinnotify);
        //     }
        // })->timezone('Asia/Kolkata')->dailyAt('10:00');
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
