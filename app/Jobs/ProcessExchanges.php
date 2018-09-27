<?php

namespace App\Jobs;

use DB;
use App\ExchangeLog;
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
        $acx = new \ccxt\acx();

      
        $coinspot    = new \ccxt\coinspot (array (
            'apiKey' => '2b06e54a0ccfc425665e731b57d25d6b',
            'secret' => 'RW0W17A9BFWMGFQQFQBUM7YAKWC7D6V767P8NHCTWAWV3RA9MPPF6NBPYE9F2P44E7PTGEL1AE4B12JAY',
        ));

        

        $currencies = ['USD', 'CAD', 'GBP'];

        $cryptos = ['BTC', 'ETH', 'LTC'];

        $exchangesArr = DB::table('exchanges')
        ->whereIn('id', [1, 2, 3, 5, 6])
        ->pluck('name', 'id');


        $coinbase->markets['BTC/CAD'] = array ( 'id' => 'btc-cad', 'symbol' => 'BTC/CAD', 'base' => 'BTC', 'quote' => 'CAD');
        $coinbase->markets['BTC/GBP'] = array ( 'id' => 'btc-gbp', 'symbol' => 'BTC/GBP', 'base' => 'BTC', 'quote' => 'GBP');

        $coinbase->markets['ETH/CAD'] = array ( 'id' => 'eth-cad', 'symbol' => 'ETH/CAD', 'base' => 'ETH', 'quote' => 'CAD');
        $coinbase->markets['ETH/GBP'] = array ( 'id' => 'eth-gbp', 'symbol' => 'ETH/GBP', 'base' => 'ETH', 'quote' => 'GBP');

        $coinbase->markets['LTC/CAD'] = array ( 'id' => 'ltc-cad', 'symbol' => 'LTC/CAD', 'base' => 'LTC', 'quote' => 'CAD');
        $coinbase->markets['LTC/GBP'] = array ( 'id' => 'ltc-gbp', 'symbol' => 'LTC/GBP', 'base' => 'LTC', 'quote' => 'GBP');


        // $res = $gdax->fetch_ticker ('BTC/GBP');

        //  print_r($exchangesArr);exit;

        foreach ($exchangesArr as $id => $val)
        {
            $exchanges = [];
            foreach ($currencies as $key => $value) 
            {
                foreach ($cryptos as $k => $crypto) 
                {
                    
                    if($val == 'Coinbase')
                    {
                        $coinbaseResult = $coinbase->fetch_ticker ($crypto.'/'.$value);
                        $buyPrice = $coinbaseResult['info']['buy']['data']['amount'];
                        $sellPrice = $coinbaseResult['info']['sell']['data']['amount'];
                    }
                    else if($val == 'Kraken')
                    {
                        if ($crypto == 'LTC' && ($value == 'CAD' || $value == 'GBP'))
                        {
                            $buyPrice = 0;
                            $sellPrice = 0;
                        }else
                        {
                            $krakenResult = $kraken->fetch_ticker ($crypto.'/'.$value);
                            $buyPrice = $krakenResult['info']['a'][0];
                            $sellPrice = $krakenResult['info']['b'][0];
                        }
                    } else if($val == 'CEX')
                    {
                        if ($value == 'CAD' || $crypto == 'LTC') {
                            $buyPrice = 0;
                            $sellPrice = 0;
                        } else {
                            $cexResult = $cex->fetch_ticker ($crypto.'/'.$value);
                            $buyPrice = $cexResult['bid'];
                            $sellPrice = $cexResult['ask'];
                        }
                    }
                    // if($val == 'GDAX')
                    // {
                    //     if($value == 'CAD')
                    //     {
                    //         $buyPrice = 0;
                    //         $sellPrice = 0;
                    //     }else
                    //     {
                    //         $gdaxResults = $gdax->fetch_ticker ('BTC/'.$value);
                    //         $buyPrice = $gdaxResults['bid'];
                    //         $sellPrice = $gdaxResults['ask'];
                    //     }

                    // }

                    // if($val == 'BITFINEX')
                    // {
                    //     if($value == 'CAD')
                    //     {
                    //         $buyPrice = 0;
                    //         $sellPrice = 0;
                    //     }else
                    //     {
                    //         if($value == 'USD')
                    //         {
                    //             $bitfinexResults = $bitfinex->fetch_ticker ('BTC/'.$value.'T');
                    //         }else
                    //         {
                    //             $bitfinexResults = $bitfinex->fetch_ticker ('BTC/'.$value);
                    //         }
                            
                    //         $buyPrice = $bitfinexResults['bid'];
                    //         $sellPrice = $bitfinexResults['ask'];
                    //     }

                    // }

                    if($val == 'coinfloor')
                    {
                        if($value == 'CAD' || $crypto != 'BTC')
                        {
                            $buyPrice = 0;
                            $sellPrice = 0;
                        }else
                        {
                            $coinFloorResults = $coinfloor->fetch_ticker ($crypto.'/'.$value);
                            $buyPrice = $coinFloorResults['ask'];
                            $sellPrice = $coinFloorResults['bid'];
                        }

                    }
                    // echo $val;

                    if($val == 'QuadrigaCX')
                    {
                        
                        if($value == 'GBP' || ($value == 'USD' && $crypto != 'BTC'))
                        {
                            $buyPrice = 0;
                            $sellPrice = 0;
                        }else
                        {
                            $quadrigacxResults = $quadrigacx->fetch_ticker ($crypto.'/'.$value);

                            // print_r($quadrigacxResults);
                            $buyPrice = $quadrigacxResults['ask'];
                            $sellPrice = $quadrigacxResults['bid'];
                        }

                    }

                    // if($val == 'acx')
                    // {
                    //     if($value == 'AUD')
                    //     {
                    //         $acxResults = $acx->fetch_ticker ('BTC/AUD');
                    //         $buyPrice = $acxResults['info']['buy'];
                    //         $sellPrice = $acxResults['info']['sell'];

                    //     }else
                    //     {
                    //         $buyPrice = 0;
                    //         $sellPrice = 0;
                    //     }

                    // }

                    // if($val == 'coinspot')
                    // {
                    //     if($value == 'AUD')
                    //     {
                    //         $coinspotResults = $coinspot->fetch_ticker ('BTC/AUD');
                    //         $buyPrice = $coinspotResults['bid'];
                    //         $sellPrice = $coinspotResults['ask'];

                    //     }else
                    //     {
                    //         $buyPrice = 0;
                    //         $sellPrice = 0;
                    //     }

                    // }

                    

                    $exchanges[$value][$crypto] = array(
                        'base' => $crypto,
                        'currency' => $value,
                        'buydata' => $buyPrice,
                        'selldata' => $sellPrice   
                    );
                }
            }

            $exchangesfinal = array(
                'name' => $val,
                'rates' => $exchanges
            );


            // print_r($exchangesfinal);

            $encodeExchanges = $exchangesfinal;

            $data = [
                'exchange_id' => $id,
                'preference' => $encodeExchanges
                
            ];

            $newData = ExchangeLog::create($data);

        }

    }

    public function failed(Exception $exception)
    {
        // Send user notification of failure, etc...
    }
}
