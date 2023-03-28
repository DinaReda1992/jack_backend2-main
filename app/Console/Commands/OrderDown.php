<?php

namespace App\Console\Commands;

use App\Jobs\OrderDownJob;
use Illuminate\Console\Command;

class OrderDown extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:order-down';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check Order Down';

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
        OrderDownJob::dispatch();
    }
}
