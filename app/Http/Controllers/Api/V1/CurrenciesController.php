<?php

namespace App\Http\Controllers\Api\V1;

use App\Currency;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CurrenciesController extends Controller
{

    protected $model;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Currency $model)
    {
        $this->model = $model;
    }

    
    public function index(Request $request)
    {
        $limit = $request->has('per_page') ? $request->get('per_page') : 10;
        $currencies = $this->model->paginate($limit);
        return response()->json($currencies, 200);
    }


    public function show($id) 
    {
        $currency = $this->model->find($id);
        return response()->json($currency, 200);
    }
}
