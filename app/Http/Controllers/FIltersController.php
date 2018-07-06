<?php

namespace App\Http\Controllers;

//use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;

use Illuminate\Http\Request;
use App\ExchangeLog;
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

    public function purifyArray($array)
    {
        $newArray = [];
        foreach ($array as $key => $value) {

            if(!empty($value)){

                $newArray[] = $value;
            }
        }
        return $newArray;
    }

   
    public function calculateData(Request $request)
    {
        //$rate = Swap::latest('EUR/USD');

        $apiKey = 'access_key=1b48ca80e794b1efaedb364f3834957c';

        $amount = $request->get('amount');
        $fromCurrency = $request->get('buy_currency');
        $toCurrency = $request->get('sell_currency');
        
        $fromExchangeSql = DB::table('exchange_logs')
        ->latest()
        ->limit(7)
        ->get();

        $toExchangeSql = $fromExchangeSql;

       // print_r($fromExchangeSql);exit;

   // http://apilayer.net/api/convert?access_key=1b48ca80e794b1efaedb364f3834957c&from=USD&to=GBP&amount=10  
        
        
        $buyPrice = 0;
        $sellPrice = 0;

        $finalBuyArr = array();
        $finalSellArr = array();
        $finalArr = array();

        //print_r($fromExchangeSql[0]->preference);exit;

        foreach ($fromExchangeSql as $key => $value)
        {
            $buyArr = array();
            $exchangeArr = json_decode($value->preference);

           // $buyArr['name'] = $exchangeArr->name;

            foreach ($exchangeArr->rates as $key => $value) 
            {

                if($key == $fromCurrency AND $value->buydata != 0){

                    $buyPrice = $value->buydata;

                    $buyArr['price'] = $value->buydata;
                    $buyArr['base'] = $value->base;
                    $buyArr['currency'] = $value->currency;
                    $buyArr['name'] = $exchangeArr->name;

                }
                
            }

            $finalBuyArr[] = $buyArr;

        }
        
        $trimedBuyArr = $this->purifyArray($finalBuyArr);

       
        foreach ($toExchangeSql as $key => $value)
        {
            $sellArr = array();
            $exchangeArr = json_decode($value->preference);

            //$sellArr['name'] = $exchangeArr->name;

            foreach ($exchangeArr->rates as $key => $value) 
            {

                if($key == $toCurrency AND $value->selldata != 0)
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

        $trimedSellArr = $this->purifyArray($finalSellArr);


        for ($i=0; $i < count($trimedBuyArr) ; $i++) { 
            
            for ($x=0; $x < count($trimedSellArr) ; $x++) {
            
                $val = (floatval($amount) / floatval($trimedBuyArr[$i]['price'])) * floatval($trimedSellArr[$x]['price']);

                // echo $val;
                // echo '\n';
                $query = "&from=$toCurrency&to=$fromCurrency&amount= $val";
                $url = 'https://apilayer.net/api/convert?'.$apiKey.$query;

                $client = new Client();
                $res = $client->get($url);
        
                $obj = json_decode($res->getBody());

                $convertedVal = $obj->result;
                $calculatedVal = $convertedVal - $amount;
                $percentage =  ($calculatedVal/$amount)*100;

                //$percentage = 2.34;

                $finalArr[] = array(
                    'buy' => $trimedBuyArr[$i],
                    'sell' => $trimedSellArr[$x],
                    'profit' => number_format((float)$percentage, 2, '.', '')

                );
            }
        }

        // uasort($finalArr, function($a, $b){
        //    // print_r($b);
        //     return strcmp($a['profit'], $b['profit']);
        // });

        //print_r($finalArr);exit;


        return response()->json([
                'data' => $finalArr,
                // 'from' => $fromCurrency,
                // 'to' => $toCurrency
        ]);
                
    }

   
}