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
    

        $exchangeSql = DB::table('exchange_data')->where('exchange_id', '=', 1)->first();

        $exchangeArr = json_decode($exchangeSql->preference);

        $buyPrice = 0;
        $sellPrice = 0;

        $jsonArr = array();

        foreach ($exchangeArr as $key => $value) 
        {
            if($key == $fromCurrency){

                $buyPrice = $value->buydata->amount;
                $jsonArr['buy'] = $value->buydata;

            }
            if($key == $toCurrency){

                $sellPrice = $value->selldata->amount;
                $jsonArr['sell'] = $value->selldata;

            }
        }

      //  print_r($jsonArr);

        //print('buy-price-'.$buyPrice);
        //print('sell-price-'.$sellPrice);
       

        if($buyPrice != 0 && $sellPrice != 0){
       
        $val = (floatval($amount) / floatval($buyPrice)) * floatval($sellPrice);


        $query = "&from=$toCurrency&to=$fromCurrency&amount= $val";
        $url = 'https://apilayer.net/api/convert?'.$apiKey.$query;



        $client = new Client();
        $res = $client->get($url);
      
        $obj = json_decode($res->getBody());

        $convertedVal = $obj->result;

        $calculatedVal = $convertedVal - $amount;

        $percentage =  ($calculatedVal/$amount)*100;

        
        $jsonArr['exchange'] = 'Coinbase';
        $jsonArr['profit'] = number_format((float)$calculatedVal, 2, '.', '');
        $jsonArr['percentage'] = number_format((float)$percentage, 2, '.', '');


        return response()->json([
            'data' => $jsonArr,
            'from' => $fromCurrency,
            'to' => $toCurrency
        ]);

        //print_r($jsonArr);
        } else {

            return response()->json([
                'data' => [],
                'error' => 'bad api request',
            ]);

        }

        
        
        
    }

    
}