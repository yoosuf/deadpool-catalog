<?php

namespace App\Http\Controllers\Api\V1;

use App\ExchangeLog;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ExchangeLogsController extends Controller
{
    protected $model;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(ExchangeLog $model)
    {
        $this->model = $model;
    }

    public function index(Request $request)
    {
        $limit =$request->has('per_page') ? $request->get('per_page') : 10;

        $exchangeLogs = $this->model;

        $historicalData = $exchangeLogs->paginate($limit);
   
        return response()->json($historicalData, 200);
    }

    public function show($id, Request $request)
    {
        $limit =$request->has('per_page') ? $request->get('per_page') : 10;

        $exchangeLogs = $this->model;

        //echo $id;exit;

        $err = 0;

        
        if ($request->has('date')) {

            $date = date('Y-m-d H:i:s', strtotime($request->get('date')));
            $historicalData = $exchangeLogs
            ->whereDate('created_at', $date)
            ->where('exchange_id', '=', $id)
            ->get();
            $err = 0;

        }else if ($request->has('datetime')) {

            $datetime = $request->get('datetime');
            $historicalData = $exchangeLogs
            ->where('created_at', $datetime)
            ->where('exchange_id', '=', $id)
            ->get();
            $err = 0;

        }else if ($request->has('from') AND $request->has('to')) {

            $from = date('Y-m-d H:i:s', strtotime($request->get('from')));
            $to = date('Y-m-d H:i:s', strtotime($request->get('to')));

            $historicalData = $exchangeLogs
            ->whereDate('created_at','>=',  $from)
            ->whereDate('created_at', '<=', $to)
            ->where('exchange_id', '=', $id)
            ->get(); 
            $err = 0;

        }else {
            
            $historicalData = $exchangeLogs
            ->where('exchange_id', '=', $id)
            ->get(); 
            $err = 0;

        }
   
        return ($err == 0) ? response()->json($historicalData, 200) : response()->json('Bad API Request', 200);
    }
}
