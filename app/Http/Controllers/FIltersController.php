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

        $fromCurrencyArr = is_array($fromCurrencyArr) ? $fromCurrencyArr[0]: $fromCurrencyArr;

        foreach ($fromExchangeSql as $k => $val)
        {
            $buyArr = array();
            $exchangeArr = json_decode($val->preference);

            foreach ($exchangeArr->rates as $key => $value) 
            {
                if($key == $fromCurrencyArr)
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

    // foreach ($currency_layer as $id => $value) 
    // {
    //     if($value->iso == $currency1)
    //     {
    //         $ratesArr = json_decode($value->other_conversion_values);
    //     }
    // }

    // $keystr = $currency1.$currency2;
    // $rate = $ratesArr->data->$keystr;

    // $val = (floatval($amount) / floatval($trimedBuyArr[$i]['price'])) * floatval($trimedSellArr[$x]['price']);
    // $convertedVal = $val/$rate;
    // $calculatedVal = $convertedVal - $amount;

    // $percentage =  ($calculatedVal/$amount)*100;

    private function prepareNextUrls($fromExchangeSql, $fromCurrencyArr, $cryptostr, $amount, $withFee, $exchanges, $url){

        $currency_layer = $this->getCurrencyLayerData();

        $sellcurrency = array();

        $crypto = ($cryptostr== 'all') ? 'all' : explode(',', $cryptostr);

        

        foreach ($fromCurrencyArr as $key1 => $currency1) {

            foreach ($fromCurrencyArr as $key2 => $currency2) {

                if($currency1 != $currency2){
                    

                    $resBuyData = $this->fillBuyData($fromExchangeSql, $currency1, $crypto);
                    
                    // echo $currency2;
                    // echo '/';
                    $sellcurrency = array();

                    $sellcurrency[] = $currency2; 
                  //  print_r($sellcurrency);
                    $resSellData = $this->fillSellData($fromExchangeSql, $sellcurrency, $crypto);
                    
                    for ($i=0; $i < count($resBuyData) ; $i++) { 

                        $buybase = $resBuyData[$i]['base'];
                        $buyExchange = $resBuyData[$i]['name'];  
                        $buyCurr = $resBuyData[$i]['currency'];
            
                        $array = [];
                        
                        for ($x=0; $x < count($resSellData) ; $x++) {
                            
                            $sellExchange = $resSellData[$x]['name'];
                            $sellbase = $resSellData[$x]['base'];
                            $selcurr = $resSellData[$x]['currency'];
            
                            foreach ($currency_layer as $id => $value) 
                            {
                                if($value->iso == $buyCurr)
                                {
                                    $ratesArr = json_decode($value->other_conversion_values);
                                }
                            }
            
                            if($buybase == $sellbase AND ($buyExchange != $sellExchange OR $buyCurr != $selcurr))
                            {
                                $keystr = $buyCurr.$selcurr;
                                $rate = $ratesArr->data->$keystr;
            
                                $val = (floatval($amount) / floatval($resBuyData[$i]['price'])) * floatval($resSellData[$x]['price']);
                                $convertedVal = $val/$rate;
                                $calculatedVal = $convertedVal - $amount;
            
                                $percentage =  ($calculatedVal/$amount)*100;
                                $percentage = ($withFee === 'true') ? $percentage-2 : $percentage;
            

                                // $array[$sellExchange][$sellbase][$selcurr] = $resSellData[$x];
                                // $array[$sellExchange][$sellbase][$selcurr]['profit'] = number_format((float)$percentage, 2, '.', '');
                                //$profitArr = 
                                $finalArr['profit'][$buyCurr.'-'.$selcurr][] = number_format((float)$percentage, 2, '.', '');
                                $finalArr['url'] [$buyCurr.'-'.$selcurr]= $url.'?currency='.$buyCurr.','.$selcurr.'&amount='.$amount.'&crypto='.$cryptostr.'&fee='.$withFee.'&exchanges='.$exchanges;
                                //$finalArr[$buybase][$buyCurr]['sell'] = $array;
            
                           }
                        }
                    }


                    //$urlsArr[$currency1.'-'.$currency2] = $url.'?currency='.$currency1.','.$currency2.'&amount='.$amount.'&crypto='.$crypto.'&fee='.$withFee.'&exchanges='.$exchanges;
               }
            }
        }
       

        $urlArray = [];

        foreach ($finalArr['profit'] as $key => $value) {
            rsort($value);
            $urlArray[$key]['url'] = $finalArr['url'][$key];
            $urlArray[$key]['profit'] = $value[0];
        }

      
     return $urlArray;
    }

    public function getGraphData($buyExchange,$sellExchange,$buybase,$sellbase,$buyCurr,$selcurr,$rate){

        $baseBuy[]= $buybase;
        $baseSell[]= $sellbase;

        $currSell[]= $selcurr;

        $date = new \DateTime();
        $date->modify('-24 hours');
        $formatted_date = $date->format('Y-m-d H:i:s');

        // echo date('Y-m-d H:i:s');exit;

        $buyData = DB::table('exchange_logs')
            ->whereJsonContains('preference->name', $buyExchange)
            // ->where('created_at', '>',$formatted_date)
            ->latest()
            ->limit(12)
            ->get();
        $sellData = DB::table('exchange_logs')
            ->where('preference->name', $sellExchange)
            // ->where('created_at', '>',$formatted_date
            ->latest()
            ->limit(12)
            ->get();

         $resBuyData = $this->fillBuyData($buyData, $buyCurr, $baseBuy);

         $resSellData = $this->fillSellData($sellData, $currSell, $baseSell);

        
        

         $percentArr = array();
         $lossArr = array();
         $chatrsArr = array();
         $timeArr = array();
         foreach ($resBuyData as $id1 => $value1) {

           // echo $value1['timestamp'];
            $time = date('H:i', strtotime($value1['timestamp']));
            $timeArr[$id1] = $time;

            $buyval = $value1['price'];
            foreach ($resSellData as $id2 => $value2) {

                $sellval = $resSellData[$id1]['price'];

                //echo $resSellData[$id1]['timestamp'];
                $amount = 100;

                $val = ($amount/ floatval($buyval)) * floatval($sellval);
                $convertedVal = $val/$rate;
                $calculatedVal = $convertedVal - $amount;

                $percentage =  ($calculatedVal/$amount)*100;

                $percentArr[$id1] = number_format((float)$percentage, 2, '.', '');
                // if($percentage > 0){

                //     $lossArr[$id1] = '0';
                //     $percentArr[$id1] = number_format((float)$percentage, 2, '.', '');

                // }else {

                //     $percentArr[$id1] = '0';
                //     $lossArr[$id1] = number_format((float)$percentage, 2, '.', '');
                // }

               
            }
         }

        //  $chatrsArr['data'] = $percentArr;
         $chatrsArr['time'] = array_reverse($timeArr);
         $chatrsArr['profits'] = array_reverse($percentArr);
        //  $chatrsArr['losses'] = array_reverse($lossArr);

        


         return $chatrsArr;
        
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

       

        // print_r($users);
        // exit;

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

                    $graphData = $this->getGraphData($buyExchange,$sellExchange,$buybase,$sellbase,$buyCurr,$selcurr, $rate);
                    //exit;
                    

                    $val = (floatval($amount) / floatval($trimedBuyArr[$i]['price'])) * floatval($trimedSellArr[$x]['price']);
                    $convertedVal = $val/$rate;
                    $calculatedVal = $convertedVal - $amount;

                    $percentage =  ($calculatedVal/$amount)*100;
                    $percentage = ($withFee === 'true') ? $percentage-2 : $percentage;

                    $array[$sellExchange][$sellbase][$selcurr] = $trimedSellArr[$x];
                    $array[$sellExchange][$sellbase][$selcurr]['profit'] = number_format((float)$percentage, 2, '.', '');
                    $array[$sellExchange][$sellbase][$selcurr]['charts'] = $graphData;

                    $finalArr[$buyExchange][$buybase][$buyCurr]['buy'] = $trimedBuyArr[$i];
                    $finalArr[$buyExchange][$buybase][$buyCurr]['sell'] = $array;
                    

               }
            }
        }

    //    print_r($finalArr);exit;

       // prepare next urls
        if(count($fromCurrencyArr) > 1){
            
            $urlsArr = $this->prepareNextUrls($fromExchangeSql, $fromCurrencyArr, $request->get('crypto'), $amount, $withFee, $exchanges, $request->url());        
        }

        //print_r($finalArr);exit;

        return response()->json([
                'data' => $finalArr,
                'meta' => array('links'=>$urlsArr),
                'number' => (count($urlsArr) > 1) ? 1: 0
        ]);
                
    }

   
}