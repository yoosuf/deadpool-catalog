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


class ExchangeLogsController extends Controller
{
    protected $model;
    private $fractal;


    /**
     * Create a new controller instance.
     *
     * @param Exchange $model
     */
    public function __construct(Exchange $model)
    {
        $this->model = $model;
        $this->fractal = new Manager();

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
            
            $historicalData = $exchangeLogs->limit($limit)->get();

            
            $dataArray = $historicalData->toArray();

            // Set response headers to trigger file download on client side
            header("Content-type: application/csv");
            header("Content-Disposition: attachment;filename=exchange_logs.csv");

            $output_file_pointer = fopen('php://output', 'w');

            // Output the CSV headers
            $headers = array(
                'Exchange',
                'Currency',
                'Crypto',
                'Buy Price',
                'Sell Price',
                'Time',
            );

            fputcsv($output_file_pointer, $headers);

            foreach($dataArray as $key => $val) {
              
                $preference = is_array($val['preference']) ? $val['preference']['rates'] : json_decode($val['preference'])->rates;

                
                foreach ($preference as $crypto => $data)
                {
                    foreach ($data as $k => $v)
                    {
                        if(is_array($val['preference']))
                        {
                            $output = array(
                                $val['preference']['name'],
                                $v['currency'],
                                $v['base'],
                                $v['buydata'],
                                $v['selldata'],
                                $val['created_at']
                            );
                            
                            fputcsv($output_file_pointer, $output);
                            ob_flush();
                            flush();

                        } else {

                           // if($v->buydata != 0){
                                $output = array(
                                    json_decode($val['preference'])->name,
                                    $v->currency,
                                    $v->base,
                                    $v->buydata,
                                    $v->selldata,
                                    $val['created_at']
                                );
                            //}
                            
                            fputcsv($output_file_pointer, $output);
                            ob_flush();
                            flush();
                        }
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