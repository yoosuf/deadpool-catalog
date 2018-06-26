<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use DB;
use App\Coinbase;
use App\ExchangeData;


include 'ccxt.php';

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
        
       // $bittrex  = new \ccxt\bittrex  (array ('verbose' => true));
//$root = dirname (dirname (dirname (__FILE__)));

       //include '/home/prabba/Workspace/welltech/deadpool-catalog/deadpool-catalog/app/ccxt.php';

    $coinbase    = new \ccxt\coinbase (array (
        'apiKey' => '7ZwQTSfbA8MNHa9F',
        'secret' => 'yswMi2RFI8dAFfPH563VafVDAXpu0ScS',
    ));

    $kraken    = new \ccxt\kraken (array (
        'apiKey' => 'j6MZpI3Xi6qiJGf4IPX2Jlnv2zGin0LL/QbpuXIT6fmjuVlp4qk2QuuA',
        'secret' => 'lWMKf43m0Jo2YrxMgjUznODLYVEo9hejheKxvtIaYw2peDirJh7o2Fd6E8Pg98Otmz+PZSkJzildHSPzOv93dg==',
    ));

    

    $currencies = ['USD', 'CAD', 'GBP'];

    $exchangesArr = DB::table('exchanges')->pluck('name', 'id');

    

    
    // $exchangeArr = [

    //         [
    //             'id'=>1,
    //             'name'=> 'Coinbase'
    //         ],
    //         [
    //             'id'=>2,
    //             'name'=> 'Kraken'
    //         ]
    // ];

    foreach ($exchangesArr as $id => $val)
    {
        $exchanges = [];
        foreach ($currencies as $key => $value) 
        {
           
            if($val == 'Coinbase')
            {

                $coinbaseResult = $coinbase->fetch_ticker ('BTC/'.$value);
                $buyPrice = $coinbaseResult['info']['buy']['data']['amount'];
                $sellPrice = $coinbaseResult['info']['sell']['data']['amount'];

            }

            if($val == 'Kraken')
            {
                $krakenResult = $kraken->fetch_ticker ('BTC/'.$value);
                $buyPrice = $krakenResult['info']['a'][0];
                $sellPrice = $krakenResult['info']['b'][0];

            }


            $exchanges[$value] = array(
                'base' => 'BTC',
                'currency' => $value,
                'buydata' => $buyPrice,
                'selldata' => $sellPrice   
            );
        }

        
        $exchangesfinal = array(

            'name' => $val,
            'rates' => $exchanges
        );
        $encodeExchanges = json_encode($exchangesfinal);

        
        
        DB::table('exchange_data')->insert(
            ['exchange_id' => $id,'preference' => $encodeExchanges]
        );

       
 
    }
   

    


    
      // BTC-USD
           
    //print_r($exchanges);
       
    }
}