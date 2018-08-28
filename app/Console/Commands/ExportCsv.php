<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Response;
use App\Http\Controllers\FIltersController;
use App\Jobs\ProcessExportCsv;
use Carbon\Carbon;
use Log;
use DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMailable;

class ExportCsv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ExportCsv:exportCsv';

    private $filter;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command export csv';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->filter = new FIltersController();
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
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
        header("Content-type: application/csv");
        header("Content-Disposition: attachment;filename=exchange_logs.csv");


        $filename = "/../../../storage/logins.csv";


        $output_file_pointer = fopen($filename, 'w');

        
   // $handle = fopen($filename, 'w');

        // // Output the CSV headers
        $headers = array(
            'Timestamp',
            'Sending exchange',
            'Receiving Exchange',
            'Sending currency',
            'Receiving currency',
            'Sending crypto',
            'Receiving crypto',
            'Sending price',
            'Receiving price',
            'Profit'
        );

        //print_r($finalBuyArr);exit;

        fputcsv($output_file_pointer, $headers);
            
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

                    $output = array(

                        $buydata['timestamp'],
                        $buyExchange,
                        $sellExchange,
                        $buyCurr,
                        $selcurr,
                        $buybase,
                        $sellbase,
                        $buydata['price'],
                        $selldata['price'],
                        number_format((float)$percentage, 2, '.', '')
                        // $val['created_at']
                    );
                    
                    fputcsv($output_file_pointer, $output);
                    // ob_flush();
                    // flush();

                }

            }

        }

        // $headers = array(

        //     'Content-type: application/csv',
        //     'Content-Disposition: attachment;filename=exchange_logs.csv'

        // );

        // $file= "logins.csv";

        // $headers = array(
        //           'Content-type: application/csv',
        //         );
    
        // return response()->download($file, 'test.csv', $headers);



        fclose($output_file_pointer);

        Mail::to('welltech90@yopmail.com')->send(new SendMailable(34));

       // Mail::raw('Raw string email', function($msg) { $msg->to(['welltech90@yopmail.com']); $msg->from(['test@test.com']); });
       //print_r($x);exit;


        // return response()->download($pathToFile, 'test.csv', $headers);

        //return response()->download('/../logins.csv');

        die;
    }
}