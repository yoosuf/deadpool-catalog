<?php

namespace App\Jobs;

use DB;
//use App\ExchangeLog;


class ProcessExportCsv extends Job
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
        echo 'hello';exit;
            $amount = 100;

            $finalBuyArr = array();
            $finalSellArr = array();
           

            $historicalBuyData = DB::table("exchange_logs")
                    ->whereDate('created_at', '>', Carbon::now()->subDays(45))
                    ->get();

            $historicalSellData = DB::table("exchange_logs")
                    ->whereDate('created_at', '>', Carbon::now()->subDays(45))
                    ->get();

           // print_r($historicalBuyData);exit;


            foreach ($historicalBuyData as $k => $val)
            {
                $buyArr = array();
                $exchangeArr = json_decode($val->preference);
    
                foreach ($exchangeArr->rates as $key => $value) 
                {
                    // if($key == $fromCurrencyArr)
                    // {
                        foreach ($value as $crypto => $data)
                        {
                           // if($cryptoCurrency == 'all') {
    
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
                            //}
                        }
                   // }
                }
            }

            


            foreach ($historicalSellData as $k => $val)
            {
               // echo $k;
                $sellArr = array();
                $exchangeArr = json_decode($val->preference);

                foreach ($exchangeArr->rates as $key => $value) 
                {
                    // if($key == $fromCurrencyArr)
                    // {
                        foreach ($value as $crypto => $data)
                        {
                            //if($cryptoCurrency == 'all') {

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
                            //}
                        }   
                    //}
                }
            }

            $currency_layer = $this->filter->getCurrencyLayerData();

            // print_r($currency_layer);
          // print_r($finalSellArr);exit;

            // Set response headers to trigger file download on client side

            // header('Content-disposition: attachment; filename=file.json');
            // header('Content-type: application/json');

            $output = [];
          
           // fputcsv($output_file_pointer, $headers);
                
            foreach ($finalBuyArr as $k1 => $buydata) {

                $buybase = $buydata['base'];
                $buyExchange = $buydata['name'];  
                $buyCurr = $buydata['currency'];
                $buyTime = $buydata['timestamp'];


                foreach ($finalSellArr as $k2 => $selldata) {


                    $sellExchange = $selldata['name'];
                    $sellbase = $selldata['base'];
                    $selcurr = $selldata['currency'];
                    $sellTime = $selldata['timestamp'];

                    //echo $sellExchange.'/';
    
                    foreach ($currency_layer as $id => $value) 
                    {
                        if($value->iso == $buyCurr)
                        {
                            $ratesArr = json_decode($value->other_conversion_values);
                        }
                    }

                    if($buybase == $sellbase AND $buyTime == $sellTime)
                    {
                    //echo $sellExchange;
                    // exit;
                        $keystr = $buyCurr.$selcurr;
                        $rate = $ratesArr->data->$keystr;

        
                        $val = (floatval($amount) / floatval($buydata['price'])) * floatval($selldata['price']);
                        $convertedVal = $val/$rate;
                        $calculatedVal = $convertedVal - $amount;

                        $percentage =  ($calculatedVal/$amount)*100;

                        $output[] = array(

                            'Timestamp'=> $buydata['timestamp'],
                            'Sending exchange'=> $buyExchange,
                            'Receiving Exchange'=> $sellExchange,
                            'Sending currency'=>$buyCurr,
                            'Receiving currency'=>$selcurr,
                            'Sending crypto'=>$buybase,
                            'Receiving crypto'=>$sellbase,
                            'Sending price'=>$buydata['price'],
                            'Receiving price'=>$selldata['price'],
                            'Profit'=>number_format((float)$percentage, 2, '.', '')
                            // $val['created_at']
                        );
                        
                        // fputcsv($output_file_pointer, $output);
                        // ob_flush();
                        // flush();

                    }

                }

            }

            //fclose($output_file_pointer);

           $json = json_encode($output);
            


            echo $json;

            

            // $fp = fopen('results.json', 'w');
            // fwrite($fp, json_encode($output));
            // fclose($fp);

            //die;
       // }

    }

    public function failed(Exception $exception)
    {
        // Send user notification of failure, etc...
    }
}
