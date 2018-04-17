<?php

namespace App\Http\Controllers\Api\V1;

use App\Entities\Crypt;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;

use App\Http\Resources\Crypt as CryptResource;


class CryptsController extends ApiController
{
    private $crypt;

    /**
     * Create a new controller instance.
     * @param Crypt $crypt
     */
    public function __construct(Crypt $crypt)
    {
        $this->crypt = $crypt;
    }

    public function index(Request $request)
    {
        $data = $this->crypt->get();
        return CryptResource::collection($data);
    }


    public function show(Request $request, $id)
    {
        $data = $this->crypt->findOrFail($id);
        return new CryptResource($data);
    }

    public function save(Request $request)
    {
        $this->validation($request);

        $data = $request->only(['name', 'code', 'symbol', 'is_active']);

        $country = $this->crypt->create($data);

        return $country;
    }


    public function update(Request $request, $id)
    {
        $country = $this->crypt->findOrFail($id);
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
