<?php

namespace App\Http\Controllers\Api\V1;

use App\Exchange;
use App\Http\Controllers\Controller;
use App\Http\Transformers\ExchangeTransformer;
use Illuminate\Http\Request;
use League\Fractal;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\Collection;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class ExchangesController extends Controller
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

    public function index(Request $request)
    {
        $limit =$request->has('per_page') ? $request->get('per_page') : 10;
        $paginatedData = $this->model->paginate($limit);
        $exchanges = $paginatedData->getCollection();
        $resource = new Collection($exchanges, new ExchangeTransformer);
        $resource->setPaginator(new IlluminatePaginatorAdapter($paginatedData));
        return $this->fractal->createData($resource)->toArray();


//        return response()->json($exchanges, 200);
    }




    public function show($id)
    {
        $exchange = $this->model->findOrFail($id);
        $resource = new Item($exchange, new ExchangeTransformer);
        return $this->fractal->createData($resource)->toArray();

//        return response()->json($exchange, 200);
    }
}
