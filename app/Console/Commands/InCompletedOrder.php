<?php

namespace App\Console\Commands;

use App\Jobs\InCompletedOrderJob;
use Illuminate\Console\Command;

class InCompletedOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'incomplete-order:notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Notification To order has More than 24 hours from creation inCompleted order ';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        InCompletedOrderJob::dispatch();
    }
}
