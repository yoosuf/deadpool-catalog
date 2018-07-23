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


class ExchangeLogsController extends Controller
{
    protected $model;

    /**
     * Create a new controller instance.
     *
     * @param Exchange $model
     */
    public function __construct(Exchange $model)
    {
        $this->model = $model;
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
 
        $err = 0;

        if ($request->has('from') AND $request->has('to')) {
            
            if($validatedData) {

                $from = date('Y-m-d H:i:s', strtotime($request->get('from')));
                $to = date('Y-m-d H:i:s', strtotime($request->get('to')));

                $historicalData = $exchangeLogs
                ->whereDate('created_at','>=',  $from)
                ->whereDate('created_at', '<=', $to)
                ->paginate($limit); 
                $err = 0;
            }

        } else {

            // dd($exchangeLogs);
            $historicalData = $exchangeLogs->paginate($limit); 
            $err = 0;
        }


        $exchanges = $historicalData->getCollection();
        $resource = new Collection($exchanges, new ExchangeLogTransformer);
        $resource->setPaginator(new IlluminatePaginatorAdapter($historicalData));
        return $this->fractal->createData($resource)->toArray();

    }

    public function show($exchangeId, $logId, Request $request)
    {
        $exchange = $this->model->find($exchangeId);
        $historicalData = $exchange->exchange_logs->find($logId);
        return response()->json($historicalData, 200);
    }
}
