<?php

namespace App\Http\Controllers\Api\V1;


use App\Country;
use App\Http\Transformers\CountryTransformer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use League\Fractal;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\Collection;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;

class CountriesController extends Controller
{
    protected $model;

    private $fractal;


    /**
     * Create a new controller instance.
     *
     * @param Country $model
     */
    public function __construct(Country $model)
    {
        $this->model = $model;
        $this->fractal = new Manager();
    }
    
    public function index(Request $request)
    {

        $limit =$request->has('per_page') ? $request->get('per_page') : 10;
        $paginatedData = $this->model->paginate($limit);
        $countries = $paginatedData->getCollection();
        $resource = new Collection($countries, new CountryTransformer);
        $resource->setPaginator(new IlluminatePaginatorAdapter($paginatedData));
        return $this->fractal->createData($resource)->toArray();


//        $limit = $request->has('per_page') ? $request->get('per_page') : 10;
//        $countries = $this->model->paginate($limit);
//        return response()->json($countries, 200);
    }

    public function show($id) 
    {

        $country = $this->model->find($id);
        $resource = new Item($country, new CountryTransformer());
        return $this->fractal->createData($resource)->toArray();


//        return response()->json($country, 200);
    }
}
