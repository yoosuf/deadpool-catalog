<?php

namespace App\Http\Controllers\Api\V1;


use App\Country;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CountriesController extends Controller
{
    protected $model;

    /**
     * Create a new controller instance.
     *
     * @param Country $model
     */
    public function __construct(Country $model)
    {
        $this->model = $model;
    }
    
    public function index(Request $request)
    {
        $limit = $request->has('per_page') ? $request->get('per_page') : 10;
        $countries = $this->model->paginate($limit);
        return response()->json($countries, 200);
    }

    public function show($id) 
    {
        $country = $this->model->find($id);
        return response()->json($country, 200);
    }
}
