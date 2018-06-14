<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\Coinbase;
use App\ExchangeData;

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
        $client = Coinbase::config();

        $currencies = ['USD', 'CAD', 'GBP'];

        foreach ($currencies as $key => $value) 
        {
            $buyPrice = $client->getBuyPrice('BTC-'.$value);
            $buyPrice = $client->decodeLastResponse();

            $sellPrice = $client->getSellPrice('BTC-'.$value);
            $sellPrice = $client->decodeLastResponse();

            $exchanges[$value] = array(
                'buydata' => $buyPrice['data'],
                'selldata' => $sellPrice['data']    
            );
        }

        $exchangesfinal = array(

            'id' => 1,
            'name' => 'coinbase',
            'rates' => $exchanges
        );


        $encodeExchanges = json_encode($exchanges);

        // $data = [
        //     'exchange_id' => 1,
        //     'preference' => $encodeExchanges
            
        // ];

        DB::table('exchange_data')->insert(
            ['exchange_id' => 1, 'preference' => $encodeExchanges]
        );
        //print_r(ExchangeData);exit;

        //$newData = ExchangeData::create($data);

       

        

       
           
          // print_r($sellPrice);
       
    }
}