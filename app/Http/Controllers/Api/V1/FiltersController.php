<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Route;
use Symfony\Bridge\PsrHttpMessage\Factory\DiactorosFactory;

use Illuminate\Http\Request;
use App\ExchangeLog;
use GuzzleHttp\Client;
use Swap\Laravel\Facades\Swap;
use DB;


class FiltersController extends Controller
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
        $apiKey = 'access_key=1b48ca80e794b1efaedb364f3834957c';

        $amount = $request->get('amount');
        $fromCurrency = $request->get('buy_currency');
        $toCurrency = $request->get('sell_currency');
        $cryptoCurrency = $request->get('crypto');

        $fromExchangeSql = DB::table('exchange_logs')
        ->latest()
        ->limit(3)
        ->get();

        //print_r($fromExchangeSql);exit;

        $toExchangeSql = $fromExchangeSql;
    
        $buyPrice = 0;
        $sellPrice = 0;

        $finalBuyArr = [];
        $finalSellArr = [];
        $finalArr = [];
        
        foreach ($fromExchangeSql as $key => $value)
        {
            $buyArr = [];
            $exchangeArr = json_decode($value->preference);

            foreach ($exchangeArr->rates as $key => $value) 
            {
                if($key == $fromCurrency)
                {
                    foreach ($value as $crypto => $data)
                    {
                        if($data->buydata != 0 AND $crypto == $cryptoCurrency)
                        {
                            $buyPrice = $data->buydata;

                            $buyArr['price'] = $data->buydata;
                            $buyArr['base'] = $data->base;
                            $buyArr['currency'] = $data->currency;
                            $buyArr['name'] = $exchangeArr->name;

                            $finalBuyArr[] = $buyArr;
                        }
                    }
                }
            }
        }

        
        $trimedBuyArr = $this->purifyArray($finalBuyArr);

        foreach ($toExchangeSql as $key => $value)
        {
            $sellArr = [];
            $exchangeArr = json_decode($value->preference);

            foreach ($exchangeArr->rates as $key => $value) 
            {
                if($key == $toCurrency)
                {
                    foreach ($value as $crypto => $data)
                    {
                        if($data->selldata != 0 AND $crypto == $cryptoCurrency)
                        {
                            $sellPrice = $data->selldata;
                            $sellArr['price'] = $data->selldata;
                            $sellArr['base'] = $data->base;
                            $sellArr['currency'] = $data->currency;
                            $sellArr['name'] = $exchangeArr->name;

                            $finalSellArr[] = $sellArr;

                        }
                    }   
                }
            }
        }

        $trimedSellArr = $this->purifyArray($finalSellArr);

        $currency_layer = DB::table('currencies')
            ->join('currency_values', 'currencies.id', '=', 'currency_values.currency_id')
            ->select('currencies.iso', 'currency_values.other_conversion_values', 'currency_values.created_at')
            ->latest()
            ->limit(3)
            ->get();

        foreach ($currency_layer as $id => $value) 
        {
            if($value->iso == $toCurrency)
            {
                $ratesArr = json_decode($value->other_conversion_values);
            }
        }

        //print_r($currency_layer);exit;

        $keystr = $toCurrency.$fromCurrency;

        $rate = $ratesArr->data->$keystr;

        for ($i=0; $i < count($trimedBuyArr) ; $i++) { 
            
            for ($x=0; $x < count($trimedSellArr) ; $x++) {
            
                $val = (floatval($amount) / floatval($trimedBuyArr[$i]['price'])) * floatval($trimedSellArr[$x]['price']);

                 //echo $val;exit;
                // echo '\n';
                // $query = "&from=$toCurrency&to=$fromCurrency&amount= $val";
                // $url = 'https://apilayer.net/api/convert?'.$apiKey.$query;

                

                // $client = new Client();
                // $res = $client->get($url);
        
                // $obj = json_decode($res->getBody());

                $convertedVal = $val*$rate;
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
        //print_r($finalArr);exit;

        return response()->json([
                'data' => $finalArr,
                'from' => $fromCurrency,
                'to' => $toCurrency
        ]);
                
    }

   
}