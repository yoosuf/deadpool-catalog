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
        $apiKey = 'access_key=1b48ca80e794b1efaedb364f3834957c';

        $amount = $request->get('amount');
        $fromCurrency = $request->get('buy_currency');
        $toCurrency = $request->get('sell_currency');
        $cryptoCurrency = $request->get('crypto');
        $withFee = $request->get('fee');

        
        $fromExchangeSql = DB::table('exchange_logs')
        ->latest()
        ->limit(3)
        ->get();

        //print_r($cryptoCurrency);exit;

        $cryptoArr = ['BTC','ETH'];
        $currencyArr = ['USD','CAD'];

        $toExchangeSql = $fromExchangeSql;
    
        $buyPrice = 0;
        $sellPrice = 0;

        $finalBuyArr = array();
        $finalSellArr = array();
        $finalArr = array();
        
        foreach ($fromExchangeSql as $k => $val)
        {
            $buyArr = array();
            $exchangeArr = json_decode($val->preference);

            foreach ($exchangeArr->rates as $key => $value) 
            {
                if($key == $fromCurrency)
                {
                    foreach ($value as $crypto => $data)
                    {
                        if($cryptoCurrency == 'all') {

                            if($data->buydata != 0)
                            {
                                $buyPrice = $data->buydata;

                                $buyArr['price'] = $data->buydata;
                                $buyArr['base'] = $data->base;
                                $buyArr['currency'] = $data->currency;
                                $buyArr['name'] = $exchangeArr->name;
                                $buyArr['timestamp'] = date('Y-m-d H:i', strtotime($val->created_at));

                                $finalBuyArr[] = $buyArr;
                            }
                        } else {

                            if($data->buydata != 0 AND $cryptoCurrency == $crypto)
                            {
                                $buyPrice = $data->buydata;

                                $buyArr['price'] = $data->buydata;
                                $buyArr['base'] = $data->base;
                                $buyArr['currency'] = $data->currency;
                                $buyArr['name'] = $exchangeArr->name;
                                $buyArr['timestamp'] = date('Y-m-d H:i', strtotime($val->created_at));

                                $finalBuyArr[] = $buyArr;
                            }

                        }
                    }
                }
            }
        }

       // print_r($finalBuyArr);exit;

        
        $trimedBuyArr = $this->purifyArray($finalBuyArr);

        foreach ($toExchangeSql as $k => $val)
        {
            $sellArr = array();
            $exchangeArr = json_decode($val->preference);

            foreach ($exchangeArr->rates as $key => $value) 
            {
                if($key == $toCurrency)
                {
                    foreach ($value as $crypto => $data)
                    {
                        if($cryptoCurrency == 'all') {

                            if($data->selldata != 0)
                            {
                                $sellPrice = $data->selldata;
                                $sellArr['price'] = $data->selldata;
                                $sellArr['base'] = $data->base;
                                $sellArr['currency'] = $data->currency;
                                $sellArr['name'] = $exchangeArr->name;
                                $sellArr['timestamp'] = date('Y-m-d H:i', strtotime($val->created_at));

                                $finalSellArr[] = $sellArr;

                            }
                        } else {

                            if($data->selldata != 0 AND $cryptoCurrency == $crypto)
                            {
                                $sellPrice = $data->selldata;
                                $sellArr['price'] = $data->selldata;
                                $sellArr['base'] = $data->base;
                                $sellArr['currency'] = $data->currency;
                                $sellArr['name'] = $exchangeArr->name;
                                $sellArr['timestamp'] = date('Y-m-d H:i', strtotime($val->created_at));

                                $finalSellArr[] = $sellArr;

                            }

                        }
                    }   
               }
            }
        }

        //print_r($finalSellArr);exit;

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

       // print_r($trimedBuyArr);exit;

       //$calculatedVal = 0;

        $keystr = $toCurrency.$fromCurrency;

        $rate = $ratesArr->data->$keystr;

        for ($i=0; $i < count($trimedBuyArr) ; $i++) { 

            $buybase = $trimedBuyArr[$i]['base'];
            $buyExchange = $trimedBuyArr[$i]['name'];
                
            $buyCurr = $trimedBuyArr[$i]['currency'];

            $array = [];
            //$calculatedValWithFees = 0;

            //$val = 0;
            
            //$calculatedVal = 0;

            for ($x=0; $x < count($trimedSellArr) ; $x++) {
                

                $sellExchange = $trimedSellArr[$x]['name'];
                $sellbase = $trimedSellArr[$x]['base'];
                $selcurr = $trimedSellArr[$x]['currency'];

                //echo $withFee;

                if($buybase == $sellbase AND ($buyExchange != $sellExchange OR $buyCurr != $selcurr))
                {
                 
                    $val = (floatval($amount) / floatval($trimedBuyArr[$i]['price'])) * floatval($trimedSellArr[$x]['price']);
                    $convertedVal = $val*$rate;
                    $calculatedVal = $convertedVal - $amount;

                
                    $percentage =  ($calculatedVal/$amount)*100;

                    $percentage = ($withFee === 'true') ? $percentage-2 : $percentage;


                    //echo $percentage.'/';
                    //echo $buyExchange.'-'.$sellExchange.'|'.$buybase.'-'.$sellbase.'/';

                    $array[$sellExchange][$sellbase] = $trimedSellArr[$x];
                    $array[$sellExchange][$sellbase]['profit'] = number_format((float)$percentage, 2, '.', '');

                    $finalArr[$buyExchange][$buybase]['buy'] = $trimedBuyArr[$i];
                    $finalArr[$buyExchange][$buybase]['sell'] = $array;

               }
                 
            }
        }

        // print_r($finalArr);exit;
    //exit;
        return response()->json([
                'data' => $finalArr,
                'from' => $fromCurrency,
                'to' => $toCurrency
        ]);
                
    }

   
}