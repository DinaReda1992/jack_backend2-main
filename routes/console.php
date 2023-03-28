<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('returnBalanceEditOrder', function () {
    $this->info("return balance editing order to user after 24 hours from editing order if user did not do action");
});

Artisan::command('incomplete-order:notification', function () {
    $this->info("Send Notification To order has More than 24 hours from creation inCompleted order");
});

Artisan::command('check:order-down', function () {
    $this->info("check order-down");
});

Artisan::command('check:order-mada-down', function () {
    $this->info("check order-mada-down");
});


Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->describe('Display an inspiring quote');
