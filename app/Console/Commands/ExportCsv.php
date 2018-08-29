<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Response;
use App\Jobs\ProcessExportCsv;
use Carbon\Carbon;
use Log;

class ExportCsv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ExportCsv:exportCsv';

    private $filter;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command export csv';

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
        Log::info("ExportCsv Queues Begins");

        //ProcessExchanges::dispatch();

        dispatch(new ProcessExportCsv());

        Log::info("ExportCsv Queues Ends");
    }
}