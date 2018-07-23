<?php

namespace App\Http\Controllers\Api\V1;

use App\Exchange;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
                ->get(); 
                $err = 0;
            }

        } else {

            // dd($exchangeLogs);
            $historicalData = $exchangeLogs->paginate(10); 
            $err = 0;
        }
   
        return ($err == 0) ? response()->json($historicalData, 200) : response()->json('Bad API Request', 400);
    }

    public function show($exchangeId, $logId, Request $request)
    {
        $exchange = $this->model->find($exchangeId);
        $historicalData = $exchange->exchange_logs->find($logId);
        return response()->json($historicalData, 200);
    }
}
