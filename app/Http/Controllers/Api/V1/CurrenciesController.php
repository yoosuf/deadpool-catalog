<?php

namespace App\Http\Controllers\Api\V1;

use App\Entities\Currency;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;

use App\Http\Resources\Currency as CurrencyResource;


class CurrenciesController extends ApiController
{
    private $currency;

    /**
     * Create a new controller instance.
     * @param Currency $currency
     */
    public function __construct(Currency $currency)
    {
        $this->currency = $currency;
    }

    public function index(Request $request)
    {
        $data = $this->currency->get();
        return CurrencyResource::collection($data);
    }


    public function show(Request $request, $id)
    {
        $data = $this->currency->findOrFail($id);
        return new CurrencyResource($data);
    }

    public function save(Request $request)
    {
        $this->validation($request);

        $data = $request->only(['name', 'code', 'symbol', 'is_active']);

        $country = $this->currency->create($data);

        return $country;
    }


    public function update(Request $request, $id)
    {
        $country = $this->currency->findOrFail($id);

        $this->validation($request);

        $data = $request->only(['name', 'code', 'symbol', 'is_active']);

        $country->update($data);

        return $country;
    }


    private function validation($request)
    {
        $this->validate($request, [
            'name'      => 'required|string',
            'code'      => 'required|string',
            'symbol'    => 'required',
            'is_active' => 'required|boolean'
        ]);

    }
}
