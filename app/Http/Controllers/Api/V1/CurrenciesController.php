<?php

namespace App\Http\Controllers\Api\V1;

use App\Currency;
use App\Http\Transformers\CurrencyTransformer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use League\Fractal;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\Collection;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;


class CurrenciesController extends Controller
{

    protected $model;

    /**
     * Create a new controller instance.
     *
     * @param Currency $model
     */
    public function __construct(Currency $model)
    {
        $this->model = $model;
    }

    
    public function index(Request $request)
    {

        $limit =$request->has('per_page') ? $request->get('per_page') : 10;
        $paginatedData = $this->model->paginate($limit);
        $currencies = $paginatedData->getCollection();
        $resource = new Collection($currencies, new CurrencyTransformer());
        $resource->setPaginator(new IlluminatePaginatorAdapter($paginatedData));
        return $this->fractal->createData($resource)->toArray();

//        $limit = $request->has('per_page') ? $request->get('per_page') : 10;
//        $currencies = $this->model->paginate($limit);
//        return response()->json($currencies, 200);
    }


    public function show($id) 
    {

        $currency = $this->model->find($id);
        $resource = new Item($currency, new CurrencyTransformer());
        return $this->fractal->createData($resource)->toArray();

//        $currency = $this->model->find($id);
//        return response()->json($currency, 200);
    }
}
