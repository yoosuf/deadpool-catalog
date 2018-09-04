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

        $coinbasePro    = new \ccxt\coinbasepro (array (
            'apiKey' => 'a9dc9bc86279373db3280e2618c6e07c',
            'secret' => 'aPMj8Ht+KKdNJlnKsrXe//oe8f/A8f9ynR4cVE2uNZ0fP/VASoW9ngd+GvrxsVR688xnF6EREuxhlLZWyFBDgg==',
            'password' => 'zs7irctmt5s'
        ));

       $params = [

            'coinbase_account_id' => '2978aa28-9d5e-47e9-aca6-a69418cb1b09'
        ];

       // $currency, $amount, $address, $params = array ()

        //print_r($params);exit;

        $coinbaseResult = $coinbasePro->fetch_ticker ('BTC/USD');
        //('USD','500', '3PUj8KFfjzhz8ySHPvGuVmJfmaWt6GpjAa',$params);

        // ($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {

        print_r($coinbaseResult);exit;

    }
}