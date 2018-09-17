<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Response;
use Carbon\Carbon;
use ccxt\ccxt;
use Log;
use DB;


class TradeCrypto extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'TradeCrypto:tradeCrypto';

    private $coinbasePro;

    protected $instrument;

    /**
     * @var
     */
    protected $console;

    /**
     * @var current book
     */
    public $book;

    public $params;

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
        //$this->filter = new FIltersController();
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        
    }
}