<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\UpdateCurrencies;
use Log;

class ConvertCurrency extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ConvertCurrency:convertCurrency';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command convert currency';

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
        Log::info("UpdateCurrencies Queues Begins");

        //ProcessExchanges::dispatch();

        dispatch(new UpdateCurrencies());

        Log::info("UpdateCurrencies Queues Ends");
         
    }
}