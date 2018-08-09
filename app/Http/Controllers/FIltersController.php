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

    private function exchangeFilter($exchanges){

        if($exchanges == 'all'){

            $fromExchangeSql = DB::table('exchange_logs')
            ->latest()
            ->limit(3)
            ->get();

        } else {

            $exchangesArr = explode(',',$exchanges);
            $limit = count($exchangesArr);
            
            $fromExchangeSql = DB::table('exchange_logs')
            ->whereIn('exchange_id', $exchangesArr)
            ->latest()
            ->limit($limit)
            ->get();
        }
        return $fromExchangeSql;
    }

    public function fillBuyData($fromExchangeSql, $fromCurrencyArr, $cryptoCurrency){

        foreach ($fromExchangeSql as $k => $val)
        {
            $buyArr = array();
            $exchangeArr = json_decode($val->preference);

            foreach ($exchangeArr->rates as $key => $value) 
            {
                if($key == $fromCurrencyArr[0])
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

                            if($data->buydata != 0 AND in_array($crypto,$cryptoCurrency))
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

        return $finalBuyArr;
    }

    public function fillSellData($toExchangeSql, $fromCurrencyArr, $cryptoCurrency){

        $fromCurrencyArr = count($fromCurrencyArr) > 1 ? $fromCurrencyArr[1] : $fromCurrencyArr[0];

        foreach ($toExchangeSql as $k => $val)
        {
            $sellArr = array();
            $exchangeArr = json_decode($val->preference);

            foreach ($exchangeArr->rates as $key => $value) 
            {
                if($key == $fromCurrencyArr)
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

                            if($data->selldata != 0 AND in_array($crypto,$cryptoCurrency))
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
        return $finalSellArr;
    }

    public function getCurrencyLayerData(){

        $currency_layer = DB::table('currencies')
        ->join('currency_values', 'currencies.id', '=', 'currency_values.currency_id')
        ->select('currencies.iso', 'currency_values.other_conversion_values', 'currency_values.created_at')
        ->latest()
        ->limit(3)
        ->get();

        return $currency_layer;
    }

    private function prepareNextUrls($fromCurrencyArr, $crypto, $amount, $withFee, $exchanges, $url){

        foreach ($fromCurrencyArr as $key1 => $currency1) {

            foreach ($fromCurrencyArr as $key2 => $currency2) {

                if($currency1 != $currency2){

                    $urlsArr[$currency1.'-'.$currency2] = $url.'?currency='.$currency1.','.$currency2.'&amount='.$amount.'&crypto='.$crypto.'&fee='.$withFee.'&exchanges='.$exchanges;
                }
            }
        }

        return $urlsArr;
    }

   
    public function calculateData(Request $request)
    {
        $amount = $request->get('amount');
        $fromCurrency = $request->get('currency');
        $withFee = $request->get('fee');
        $exchanges = $request->get('exchanges');
        $cryptoCurrency = ($request->get('crypto')== 'all') ? 'all' : explode(',', $request->get('crypto'));
        $fromCurrencyArr = explode(',',$fromCurrency);
        //$countries = $request->get('countries');
        //$toCurrency = $request->get('sell_currency');

       // print_r($fromCurrencyArr);exit;

        $buyPrice = 0;
        $sellPrice = 0;

        $finalBuyArr = array();
        $finalSellArr = array();
        $finalArr = array();
        $currencyArr = array();
        $urlsArr = array();

        // exchange filter
        $fromExchangeSql = $this->exchangeFilter($exchanges);

        $toExchangeSql = $fromExchangeSql;
    
        // filling the buy array
        $finalBuyArr = $this->fillBuyData($fromExchangeSql, $fromCurrencyArr, $cryptoCurrency);
        $trimedBuyArr = $this->purifyArray($finalBuyArr);

        // filling the sell array
        $finalSellArr = $this->fillSellData($toExchangeSql, $fromCurrencyArr, $cryptoCurrency);
        $trimedSellArr = $this->purifyArray($finalSellArr);

        // get currency layer data
        $currency_layer = $this->getCurrencyLayerData();


        for ($i=0; $i < count($trimedBuyArr) ; $i++) { 

            $buybase = $trimedBuyArr[$i]['base'];
            $buyExchange = $trimedBuyArr[$i]['name'];  
            $buyCurr = $trimedBuyArr[$i]['currency'];

            $array = [];
            
            for ($x=0; $x < count($trimedSellArr) ; $x++) {
                
                $sellExchange = $trimedSellArr[$x]['name'];
                $sellbase = $trimedSellArr[$x]['base'];
                $selcurr = $trimedSellArr[$x]['currency'];

                foreach ($currency_layer as $id => $value) 
                {
                    if($value->iso == $buyCurr)
                    {
                        $ratesArr = json_decode($value->other_conversion_values);
                    }
                }

                //echo $withFee;

                if($buybase == $sellbase AND ($buyExchange != $sellExchange OR $buyCurr != $selcurr))
                {
                    $keystr = $buyCurr.$selcurr;
                    $rate = $ratesArr->data->$keystr;

                    $val = (floatval($amount) / floatval($trimedBuyArr[$i]['price'])) * floatval($trimedSellArr[$x]['price']);
                    $convertedVal = $val/$rate;
                    $calculatedVal = $convertedVal - $amount;

                    $percentage =  ($calculatedVal/$amount)*100;
                    $percentage = ($withFee === 'true') ? $percentage-2 : $percentage;

                    $array[$sellExchange][$sellbase][$selcurr] = $trimedSellArr[$x];
                    $array[$sellExchange][$sellbase][$selcurr]['profit'] = number_format((float)$percentage, 2, '.', '');

                    $finalArr[$buyExchange][$buybase][$buyCurr]['buy'] = $trimedBuyArr[$i];
                    $finalArr[$buyExchange][$buybase][$buyCurr]['sell'] = $array;

               }
            }
        }

        // prepare next urls
        if(count($fromCurrencyArr) > 1){
            $urlsArr = $this->prepareNextUrls($fromCurrencyArr, $request->get('crypto'), $amount, $withFee, $exchanges, $request->url());
        }

        return response()->json([
                'data' => $finalArr,
                'meta' => array(
                    'links'=>$urlsArr
                ),
        ]);
                
    }

   
}