<?php

namespace App\Http\Controllers\Api\V1;

use App\Exchange;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ExchangesController extends Controller
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

    public function index(Request $request)
    {
        $limit =$request->has('per_page') ? $request->get('per_page') : 10;


        $exchanges = $this->model;
        

        $exchanges = $exchanges->paginate($limit);

        return response()->json($exchanges, 200);
    }


    public function show($id, Request $request)
    {
        $exchange = $this->model->findOrFail($id);
        return response()->json($exchange, 200);
    }
}
