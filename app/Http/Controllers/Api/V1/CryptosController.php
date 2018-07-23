<?php

namespace App\Http\Controllers\Api\V1;

use App\Crypto;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class CryptosController extends Controller
{
    protected $model;

    /**
     * Create a new controller instance.
     *
     * @param Crypto $model
     */
    public function __construct(Crypto $model)
    {
        $this->model = $model;
    }


    
    
    public function index(Request $request)
    {
        $limit = $request->has('per_page') ? $request->get('per_page') : 10;
        $cryptos = $this->model->paginate($limit);
        return response()->json($cryptos, 200);
    }

    public function show($id) 
    {
        $crypto = $this->model->find($id);
        return response()->json($crypto, 200);
    }
}
