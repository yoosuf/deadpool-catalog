<?php

namespace App\Http\Controllers\Api\V1;

use App\Exchange;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DateTime;

class ExchangeLogsController extends Controller
{
    protected $model;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Exchange $model)
    {
        $this->model = $model;
    }


    private function validateDate($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    public function index($exchangeId, Request $request)
    {
        $limit = $request->has('per_page') ? $request->get('per_page') : 10;
        $exchange = $this->model->find($exchangeId);

        $exchangeLogs = $exchange->exchange_logs();
 
        $err = 0;
        if ($request->has('date')) {

            $checkDate = $this->validateDate($request->get('date'));

            if($checkDate) {

                $date = date('Y-m-d H:i:s', strtotime($request->get('date')));
                $historicalData = $exchangeLogs
                ->whereDate('created_at', $date)
                ->get();

                $err = 0;
            } else {
                $err = 1;
            }
            

        } else if ($request->has('from') AND $request->has('to')) {
            $from = date('Y-m-d H:i:s', strtotime($request->get('from')));
            $to = date('Y-m-d H:i:s', strtotime($request->get('to')));
            $historicalData = $exchangeLogs
            ->whereDate('created_at','>=',  $from)
            ->whereDate('created_at', '<=', $to)
            ->get(); 
            $err = 0;
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
