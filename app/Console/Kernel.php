<?php

namespace App\Console;

use App\Console\Commands\InCompletedOrder;
use App\Console\Commands\OrderDown;
use App\Console\Commands\OrderMadaDown;
use App\Console\Commands\ReturnBalanceEditOrder;
use App\Models\Balance;
use App\Models\DamageEstimate;
use App\Models\DeviceTokens;
use App\Models\Notification;
use App\Models\PricingOrder;
use App\Models\Reservations;
use App\Models\Settings;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        ReturnBalanceEditOrder::class,
        InCompletedOrder::class,
        OrderDown::class,
        OrderMadaDown::class,
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();
        $schedule->command('returnBalanceEditOrder')->everyMinute();
        $schedule->command('incomplete-order:notification')->dailyAt('16:00');
        $schedule->command('check:order-down')->everyMinute();
        $schedule->command('check:order-mada-down')->everyMinute();

        // $schedule->call(function (){
        //     foreach (PricingOrder::where('payment_method','<>',0)->where('status',0)->get() as $pricing_order) {
        //         $pricing_time=Settings::where('option_name','close_pricing_time')->first()->value;

        //         if($pricing_time && $pricing_time!=0){
        //             $newdate=$pricing_order->created_at->addHours((int)$pricing_time);
        //             if($newdate <= Carbon::now()) {
        //                 $pricing_order->status=4;
        //                 $pricing_order->save();
        //             }
        //         }
        //     }
        //     foreach (DamageEstimate::where('payment_method','<>',0)->where('status',0)->get() as $damage_order) {
        //         $damage_time=Settings::where('option_name','close_damage_time')->first()->value;
        //         if($damage_time && $damage_time!=0){
        //             $newdate=$damage_order->created_at->addHours((int)$damage_time);
        //             if($newdate <= Carbon::now()) {
        //                 $damage_order->status=4;
        //                 $damage_order->save();
        //             }
        //         }
        //     }
        // })->hourly();
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
