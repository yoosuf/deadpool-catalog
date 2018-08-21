<?php

namespace App\Http\Controllers\Api\V1;

use App\Exchange;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Transformers\ExchangeLogTransformer;
use League\Fractal;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\Collection;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use OzdemirBurak\JsonCsv\File\Json;
use App\Http\Controllers\FIltersController;
use Carbon\Carbon;
use DB;


class ExchangeLogsController extends Controller
{
    protected $model;
    private $fractal;
    private $filter;


    /**
     * Create a new controller instance.
     *
     * @param Exchange $model
     */
    public function __construct(Exchange $model)
    {
        $this->model = $model;
        $this->fractal = new Manager();
        $this->filter = new FIltersController();

    }

    public function index($exchangeId, Request $request)
    {
        $limit = $request->has('per_page') ? $request->get('per_page') : 10;
        $exchange = $this->model->find($exchangeId);
        $validatedData = $this->validate($request, [
            'date' => 'date_format:"Y-m-d"',
            'from' => 'date_format:"Y-m-d"',
            'to' => 'date_format:"Y-m-d"'
        ]);
        $exchangeLogs = $exchange->exchange_logs();
        if ($request->has('from') AND $request->has('to')) {
            if($validatedData) {
                $from = date('Y-m-d H:i:s', strtotime($request->get('from')));
                $to = date('Y-m-d H:i:s', strtotime($request->get('to')));
                $historicalData = $exchangeLogs
                ->whereDate('created_at','>=',  $from)
                ->whereDate('created_at', '<=', $to);
            }
        }


        /**
         * Based on the users request type=csv downloading the data
         */
        if ($request->has('type') && $request->get('type') == "csv") {

            // $historicalData = $exchangeLogs->limit($limit)->get();

            $amount = 100;

            $finalBuyArr = array();
            $finalSellArr = array();
           

            $historicalBuyData = DB::table("exchange_logs")
                    ->whereDate('created_at', '>', Carbon::now()->subDays(30))
                    ->get();

            $historicalSellData = DB::table("exchange_logs")
                    ->whereDate('created_at', '>', Carbon::now()->subDays(30))
                    ->get();


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

            $output_file_pointer = fopen('php://output', 'w');

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
                        ob_flush();
                        flush();

                    }

                }

            }

            fclose($output_file_pointer);
            die;
        }
       

        /**
         * render as JSON
         */
        $historicalData = $exchangeLogs->paginate($limit);
        $exchanges = $historicalData->getCollection();
        $resource = new Collection($exchanges, new ExchangeLogTransformer);
        $resource->setPaginator(new IlluminatePaginatorAdapter($historicalData));
        return $this->fractal->createData($resource)->toArray();
    }

    public function show($exchangeId, $logId, Request $request)
    {
        $exchange = $this->model->find($exchangeId);
        $historicalData = $exchange->exchange_logs->find($logId);
        $resource = new Item($historicalData, new ExchangeLogTransformer);
        return $this->fractal->createData($resource)->toArray();


//        $historicalData = $exchange->exchange_logs->find($logId);
//        return response()->json($historicalData, 200);
    }
}