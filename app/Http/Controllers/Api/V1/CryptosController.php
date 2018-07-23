<?php

namespace App\Http\Controllers\Api\V1;

use App\Crypto;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use League\Fractal;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\Collection;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use App\Http\Transformers\CryptoTransformer;


class CryptosController extends Controller
{
    protected $model;


    private $fractal;


    /**
     * Create a new controller instance.
     *
     * @param Crypto $model
     */
    public function __construct(Crypto $model)
    {
        $this->model = $model;
        $this->fractal = new Manager();
    }


    
    
    public function index(Request $request)
    {

        $limit =$request->has('per_page') ? $request->get('per_page') : 10;
        $paginatedData = $this->model->paginate($limit);
        $cryptos = $paginatedData->getCollection();
        $resource = new Collection($cryptos, new CryptoTransformer);
        $resource->setPaginator(new IlluminatePaginatorAdapter($paginatedData));
        return $this->fractal->createData($resource)->toArray();

//
//        $limit = $request->has('per_page') ? $request->get('per_page') : 10;
//        $cryptos = $this->model->paginate($limit);
//        return response()->json($cryptos, 200);
    }

    public function show($id) 
    {

        $country = $this->model->find($id);
        $crypto = new Item($country, new CryptoTransformer);
        return $this->fractal->createData($crypto)->toArray();

//        $crypto = $this->model->find($id);
//        return response()->json($crypto, 200);
    }
}
