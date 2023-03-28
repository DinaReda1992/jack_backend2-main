<?php

namespace App\Console\Commands;

use App\Jobs\OrderMadaDownJob;
use Illuminate\Console\Command;

class OrderMadaDown extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:order-mada-down';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'check order mada down';

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
        OrderMadaDownJob::dispatch();
    }
}
