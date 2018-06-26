<?php

namespace App\Http\Controllers;

//use App\User;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\ExchangeData;

use GuzzleHttp\Client;

use Swap\Laravel\Facades\Swap;
use DB;

//use Swap;

class FIltersController extends Controller
{
    /**
     * Show the profile for the given user.
     *
     * @param  int  $id
     * @return Response
     */
    public function getCurrencies()
    {
        return response()->json([
            'name' => 'Abigail',
            'state' => 'CA'
        ]);
    }

   
    public function calculateData(Request $request)
    {
        //$rate = Swap::latest('EUR/USD');

        $apiKey = 'access_key=1b48ca80e794b1efaedb364f3834957c';

        $amount = $request->get('amount');
        $fromCurrency = $request->get('buy_currency');
        $toCurrency = $request->get('sell_currency');
        

        $fromExchangeSql = DB::table('exchange_data')->get();

        $toExchangeSql = DB::table('exchange_data')->get();

        
        
        $buyPrice = 0;
        $sellPrice = 0;

        $finalBuyArr = array();
        $finalSellArr = array();
        $finalArr = array();


        foreach ($fromExchangeSql as $key => $value)
        {
            $buyArr = array();
            $exchangeArr = json_decode($value->preference);

            //print_r($exchangeArr);


           // $buyArr['name'] = $exchangeArr->name;

            foreach ($exchangeArr->rates as $key => $value) 
            {

                if($key == $fromCurrency){

                    $buyPrice = $value->buydata;

                    $buyArr['price'] = $value->buydata;
                    $buyArr['base'] = $value->base;
                    $buyArr['currency'] = $value->currency;
                    $buyArr['name'] = $exchangeArr->name;

                }
                
            }

            $finalBuyArr[] = $buyArr;

        }

       

        foreach ($toExchangeSql as $key => $value)
        {
            $sellArr = array();
            $exchangeArr = json_decode($value->preference);

            //$sellArr['name'] = $exchangeArr->name;

            foreach ($exchangeArr->rates as $key => $value) 
            {

                if($key == $toCurrency)
                {
                    $sellPrice = $value->selldata;
                    
                    $sellArr['price'] = $value->selldata;
                    $sellArr['base'] = $value->base;
                    $sellArr['currency'] = $value->currency;
                    $sellArr['name'] = $exchangeArr->name;

                }

                

            }

            $finalSellArr[] = $sellArr;

        }

        for ($i=0; $i < count($finalBuyArr) ; $i++) { 
            
            for ($x=0; $x < count($finalSellArr) ; $x++) { 
            
                 //echo $finalBuyArr[$i]['price'].'-----'.$finalSellArr[$x]['price']. PHP_EOL;
                $val = (floatval($amount) / floatval($finalBuyArr[$i]['price'])) * floatval($finalSellArr[$x]['price']);

                
                $query = "&from=$toCurrency&to=$fromCurrency&amount= $val";
                $url = 'https://apilayer.net/api/convert?'.$apiKey.$query;

                $client = new Client();
                $res = $client->get($url);
        
                $obj = json_decode($res->getBody());

                $convertedVal = $obj->result;
                $calculatedVal = $convertedVal - $amount;
                $percentage =  ($calculatedVal/$amount)*100;

                $finalArr[] = array(
                    'buy' => $finalBuyArr[$i],
                    'sell' => $finalSellArr[$x],
                    'profit' => number_format((float)$percentage, 2, '.', '')

                );
            }
        }

        uasort($finalArr, function($a, $b){
           // print_r($b);
            return strcmp($a['profit'], $b['profit']);
        });

        //print_r($finalArr);


        return response()->json([
                'data' => $finalArr,
                // 'from' => $fromCurrency,
                // 'to' => $toCurrency
        ]);
                
    }

   
}