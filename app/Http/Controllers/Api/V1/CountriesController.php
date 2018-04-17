<?php

namespace App\Http\Controllers\Api\V1;

use App\Entities\Country;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Http\Resources\Country as CountryResource;


class CountriesController extends ApiController
{

    private $country;


    /**
     * Create a new controller instance.
     *
     * @param Country $country
     */
    public function __construct(Country $country)
    {
        $this->country = $country;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $data = $this->country->all();
        return CountryResource::collection($data);
    }


    /**
     * @param Request $request
     * @param $id
     * @return CountryResource
     */
    public function show(Request $request, $id)
    {
        $data = $this->country->findOrFail($id);
        return new CountryResource($data);
    }

    public function save(Request $request)
    {
        $this->validation($request);

        $data = $request->only(['name', 'nice_name', 'iso', 'iso3', 'phone_code', 'is_active']);

        $country = $this->country->create($data);

        return $country;
    }


    public function update(Request $request, $id)
    {
        $country = $this->country->findOrFail($id);

        $this->validation($request);

        $data = $request->only(['name', 'nice_name', 'iso', 'iso3', 'phone_code', 'is_active']);

        $country->update($data);

        return $country;
    }


    private function validation($request)
    {
        $this->validate($request, [
            'name'          => 'required|string',
            'nice_name'     => 'required|string',
            'iso'           => 'required|string',
            'iso3'          => 'required|string',
            'phone_code'    => 'required|numeric',
            'is_active'     => 'required|boolean'
        ]);

    }

}
