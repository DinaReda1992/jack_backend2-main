<?php

namespace App\Console\Commands;

use App\Jobs\EditOrdersJob;
use Illuminate\Console\Command;

class ReturnBalanceEditOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'returnBalanceEditOrder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'return balance editing order to user after 24 hours from editing order if user did not do action';

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
        EditOrdersJob::dispatch();
    }
}
