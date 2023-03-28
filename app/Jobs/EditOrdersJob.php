<?php

namespace App\Jobs;

use App\Models\Orders;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class EditOrdersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // delete orders after 24 hours from editing order if user did not do action

        $orders = Orders::where('is_edit', 1)
            ->where('financial_date', '!=', null)
            ->where('updated_at', '<', now()->subDay())
            ->where('edit_date', null)
            ->get();
        foreach ($orders as $order) {
            $order->returnBalance();
        }

        $orders = Orders::where('is_edit', 1)
            ->where('financial_date', '!=', null)
            ->where('edit_date', '<', now()->subDay())
            ->get();
        foreach ($orders as $order) {
            $order->returnBalance();
        }
    }
}
