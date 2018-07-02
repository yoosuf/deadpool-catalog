<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\ProcessExchanges;
use Log;

class UpdateExchanges extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'UpdateExchanges:updateExchanges';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command update exchanges';

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
     * @return mixed
     */
    public function handle()
    {
        Log::info("ProcessExchanges Queues Begins");

        //ProcessExchanges::dispatch();

        dispatch(new ProcessExchanges());

        Log::info("ProcessExchanges Queues Ends");
       
    }
}