<?php

namespace App\Jobs;

use DB;
use App\ExchangeData;
use ccxt\ccxt;


class ProcessExchanges extends Job
{
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $coinbase    = new \ccxt\coinbase (array (
            'apiKey' => '7ZwQTSfbA8MNHa9F',
            'secret' => 'yswMi2RFI8dAFfPH563VafVDAXpu0ScS',
        ));

        $kraken    = new \ccxt\kraken (array (
            'apiKey' => 'j6MZpI3Xi6qiJGf4IPX2Jlnv2zGin0LL/QbpuXIT6fmjuVlp4qk2QuuA',
            'secret' => 'lWMKf43m0Jo2YrxMgjUznODLYVEo9hejheKxvtIaYw2peDirJh7o2Fd6E8Pg98Otmz+PZSkJzildHSPzOv93dg==',
        ));

        $bitfinex    = new \ccxt\bitfinex ();
        $cex = new \ccxt\cex ();
        $gdax = new \ccxt\gdax ();
        $coinfloor = new \ccxt\coinfloor (); 
        $quadrigacx = new \ccxt\quadrigacx ();


        // $res = $quadrigacx->fetch_ticker('BTC/USD');
        // print_r($res);exit;


        $currencies = ['USD', 'CAD', 'GBP'];

        $exchangesArr = DB::table('exchanges')->pluck('name', 'id');

        $coinbase->markets['BTC/CAD'] = array ( 'id' => 'btc-cad', 'symbol' => 'BTC/CAD', 'base' => 'BTC', 'quote' => 'CAD');
        $coinbase->markets['BTC/GBP'] = array ( 'id' => 'btc-gbp', 'symbol' => 'BTC/GBP', 'base' => 'BTC', 'quote' => 'GBP');

    
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

                if($val == 'Cex')
                {
                    if($value == 'CAD')
                    {
                        $buyPrice = 0;
                        $sellPrice = 0;
                    }else
                    {
                        $cexResult = $cex->fetch_ticker ('BTC/'.$value);
                        $buyPrice = $cexResult['bid'];
                        $sellPrice = $cexResult['ask'];
                    }

                }
                if($val == 'GDAX')
                {
                    if($value == 'CAD')
                    {
                        $buyPrice = 0;
                        $sellPrice = 0;
                    }else
                    {
                        $gdaxResults = $gdax->fetch_ticker ('BTC/'.$value);
                        $buyPrice = $gdaxResults['bid'];
                        $sellPrice = $gdaxResults['ask'];
                    }

                }

                if($val == 'BITFINEX')
                {
                    if($value == 'CAD')
                    {
                        $buyPrice = 0;
                        $sellPrice = 0;
                    }else
                    {
                        if($value == 'USD')
                        {
                            $bitfinexResults = $bitfinex->fetch_ticker ('BTC/'.$value.'T');
                        }else
                        {
                            $bitfinexResults = $bitfinex->fetch_ticker ('BTC/'.$value);
                        }
                        
                        $buyPrice = $bitfinexResults['bid'];
                        $sellPrice = $bitfinexResults['ask'];
                    }

                }

                if($val == 'coinfloor')
                {
                    if($value == 'CAD')
                    {
                        $buyPrice = 0;
                        $sellPrice = 0;
                    }else
                    {
                        $coinFloorResults = $coinfloor->fetch_ticker ('BTC/'.$value);
                        $buyPrice = $coinFloorResults['bid'];
                        $sellPrice = $coinFloorResults['ask'];
                    }

                }

                if($val == 'QuadrigaCX')
                {
                    if($value == 'GBP')
                    {
                        $buyPrice = 0;
                        $sellPrice = 0;
                    }else
                    {
                        $quadrigacxResults = $quadrigacx->fetch_ticker ('BTC/'.$value);
                        $buyPrice = $quadrigacxResults['bid'];
                        $sellPrice = $quadrigacxResults['ask'];
                    }

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

            //print_r($exchangesfinal);
            $encodeExchanges = json_encode($exchangesfinal);

            
            DB::table('exchange_data')->insert(
                ['exchange_id' => $id,'preference' => $encodeExchanges]
            );

        }
    }

    public function failed(Exception $exception)
    {
        // Send user notification of failure, etc...
    }
}
