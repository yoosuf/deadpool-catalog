<?php

namespace App\Http\Controllers\Api\V1;

use App\Barakat\BitOasisExchange;
use App\Barakat\CexExchange;
use App\Barakat\CoinFloorExchange;
use App\Barakat\ConSecureExchange;
use App\Entities\Country;
use App\Entities\Crypt;
use App\Entities\Currency;
use App\Entities\Exchange;


use App\Utils\GoogleExchange;


use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;

use App\Http\Resources\Exchange as ExchangeResource;


class ExchangesController extends ApiController
{
    private $exchange;
    private $country;
    private $currency;
    private $crypt;

    /**
     * Create a new controller instance.
     * @param Exchange $exchange
     * @param Country $country
     * @param Currency $currency
     * @param Crypt $crypt
     */
    public function __construct(Exchange $exchange, Country $country, Currency $currency, Crypt $crypt)
    {
        $this->exchange = $exchange;
        $this->country = $country;
        $this->currency = $currency;
        $this->crypt = $crypt;
    }


    /**
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Collection|mixed|static[]
     */
    public function index(Request $request)
    {
        $data = $this->exchange->all();
        return ExchangeResource::collection($data);
    }


    /**
     * @param Request $request
     * @param $id
     * @return ExchangeResource
     */
    public function show(Request $request, $id)
    {
        $data = $this->exchange->findOrFail($id);
        return new ExchangeResource($data);
    }

    /**
     * @param Request $request
     * @return $this|\Illuminate\Database\Eloquent\Model
     */
    public function save(Request $request)
    {
       $this->validateRequest($request);

        $currency = $this->currency->where('code', $request->get('currency'))->firstOrFail();

        $country = $this->country->where('iso3', $request->get('country'))->firstOrFail();

        $options = $request->has('options') ? $request->get('options') : null;

        $options_data = collect([]);

        foreach ($options as $option_initial) {

            $fees_collet = collect([]);

            foreach ($option_initial['fees'] as $fee)
            {
                $fees_option_collet = collect([]);
                foreach ($fee['options'] as $option)
                {
                    // dd($option['fee_range']);
                    $fees_option_collet->push([
                        'value' => !empty($option['value']) ? $option['value'] : "",
                        'type' => !empty($option['type']) ? $option['type'] : "",
                        'is_percentage' => $option['is_percentage'],
                        'is_active' => $option['is_active'],
                    ]);
                }
                $fees_collet->push([
                    'stage' => !empty($fee['stage']) ? $fee['stage'] : "",
                    'name' => !empty($fee['name']) ? $fee['name'] : "",
                    'is_active' => $fee['is_active'],
                    'options' => $fees_option_collet,
                ]);
            }
            $options_data->push([
                "name" => !empty($option_initial['name']) ? $option_initial['name'] : "",
                "is_active" => $option_initial['is_active'],
                "fees" => $fees_collet
            ]);
        }

        $data = [
            'name' => $request->has('name') ? $request->get('name') : null,
            'description' => $request->has('description') ? $request->get('description') : null,
            'base_url' => $request->has('base_url') ? $request->get('base_url') : null,
            'preferences' => $options_data->toArray(),
            'currency_id' => $currency->id,
            'country_id' => $country->id,
            'is_active' => $request->has('is_active') ? $request->get('is_active') : null,
        ];

        $crypt_currencies = $request->has('cryptos') ? $request->get('cryptos') : null;

        $crypt_data = [];

        foreach ($crypt_currencies as $crypt_currency) {

            $crypt = $this->crypt->where('code', $crypt_currency['type'])->firstOrFail();

            $fees_collet = collect([]);

            foreach ($crypt_currency['fees'] as $fee)
            {
                $fees_option_collet = collect([]);
                foreach ($fee['options'] as $option)
                {
                    // dd($option['fee_range']);
                    $fees_option_collet->push([
                        'value' => !empty($option['value']) ? $option['value'] : "",
                        'type' => !empty($option['type']) ? $option['type'] : "",
                        'is_percentage' => $option['is_percentage'],
                        'is_active' => $option['is_active'],
                    ]);
                }
                $fees_collet->push([
                    'stage' => !empty($fee['stage']) ? $fee['stage'] : "",
                    'name' => !empty($fee['name']) ? $fee['name'] : "",
                    'is_active' => $fee['is_active'],
                    'options' => $fees_option_collet,
                ]);
            }

            $crypt_data[$crypt->id] = [
                "preferences" => json_encode([
                    "api_url" => !empty($crypt_currency['api_url']) ? $crypt_currency['api_url'] : "",
                    "api_version" => !empty($crypt_currency['api_version']) ? $crypt_currency['api_version'] : "",
                    "fees" => $fees_collet
                ]),
                "is_active" => $crypt_currency['is_active']
            ];
        }

        $exchange = $this->exchange->create($data);

        $exchange->crypts()->attach($crypt_data);

        return new ExchangeResource($exchange);
    }


    public function update(Request $request, $id)
    {
        $exchange = $this->exchange->findOrFail($id);

//        $this->validation($request);

        $currency = $this->currency->where('code', $request->get('currency'))->firstOrFail();

        $country = $this->country->where('iso3', $request->get('country'))->firstOrFail();

        $options = $request->has('options') ? $request->get('options') : null;

        $options_data = collect([]);

        foreach ($options as $option_initial) {

            $fees_collet = collect([]);

            foreach ($option_initial['fees'] as $fee)
            {
                $fees_option_collet = collect([]);
                foreach ($fee['options'] as $option)
                {
                    // dd($option['fee_range']);
                    $fees_option_collet->push([
                        'value' => !empty($option['value']) ? $option['value'] : "",
                        'type' => !empty($option['type']) ? $option['type'] : "",
                        'is_percentage' => $option['is_percentage'],
                        'is_active' => $option['is_active'],
                        'fee_range' => 
                        [
                            'is_active' => !empty($option['fee_range']) && !empty($option['fee_range']['is_active']) ? $option['fee_range']['is_active'] : false,
                            'min' => !empty($option['fee_range']) && !empty($option['fee_range']['min']) ? $option['fee_range']['min'] : 0,
                            'max' => !empty($option['fee_range']) && !empty($option['fee_range']['max']) ? $option['fee_range']['max'] : 0
                        ]
                    ]);
                }
                $fees_collet->push([
                    'stage' => !empty($fee['stage']) ? $fee['stage'] : "",
                    'name' => !empty($fee['name']) ? $fee['name'] : "",
                    'is_active' => $fee['is_active'],
                    'options' => $fees_option_collet,
                ]);
            }
            $options_data->push([
                "name" => !empty($option_initial['name']) ? $option_initial['name'] : "",
                "is_active" => $option_initial['is_active'],
                "fees" => $fees_collet
            ]);
        }

        $data = [
            'name' => $request->has('name') ? $request->get('name') : null,
            'description' => $request->has('description') ? $request->get('description') : null,
            'base_url' => $request->has('base_url') ? $request->get('base_url') : null,
            'preferences' => $options_data->toArray(),
            'currency_id' => $currency->id,
            'country_id' => $country->id,
            'is_active' => $request->has('is_active') ? $request->get('is_active') : null,
        ];

        $crypt_currencies = $request->has('cryptos') ? $request->get('cryptos') : null;

        $crypt_data = [];

        foreach ($crypt_currencies as $crypt_currency) {

            $crypt = $this->crypt->where('code', $crypt_currency['type'])->firstOrFail();

            $fees_collet = collect([]);

            foreach ($crypt_currency['fees'] as $fee)
            {
                $fees_option_collet = collect([]);
                foreach ($fee['options'] as $option)
                {
                    // dd($option['fee_range']);
                    $fees_option_collet->push([
                        'value' => !empty($option['value']) ? $option['value'] : "",
                        'type' => !empty($option['type']) ? $option['type'] : "",
                        'is_percentage' => $option['is_percentage'],
                        'is_active' => $option['is_active'],
                        'fee_range' => 
                        [
                            'is_active' => !empty($option['fee_range']) && !empty($option['fee_range']['is_active']) ? $option['fee_range']['is_active'] : false,
                            'min' => !empty($option['fee_range']) && !empty($option['fee_range']['min']) ? $option['fee_range']['min'] : 0,
                            'max' => !empty($option['fee_range']) && !empty($option['fee_range']['max']) ? $option['fee_range']['max'] : 0
                        ]
                    ]);
                }
                $fees_collet->push([
                    'stage' => !empty($fee['stage']) ? $fee['stage'] : "",
                    'name' => !empty($fee['name']) ? $fee['name'] : "",
                    'is_active' => $fee['is_active'],
                    'options' => $fees_option_collet,
                ]);
            }

            $crypt_data[$crypt->id] = [
                "preferences" => json_encode([
                    "api_url" => !empty($crypt_currency['api_url']) ? $crypt_currency['api_url'] : "",
                    "api_version" => !empty($crypt_currency['api_version']) ? $crypt_currency['api_version'] : "",
                    "fees" => $fees_collet
                ]),
                "is_active" => $crypt_currency['is_active']
            ];
        }

        $exchange->crypts()->sync($crypt_data);

        $exchange->update($data);

        return new ExchangeResource($exchange);
    }
    


    private function validateRequest($request)
    {
        $this->validate($request, [

            'name' => 'required|unique:exchanges,name',
            'currency' => 'required|exists:currencies,code',
            'country' => 'required|exists:countries,iso3',
            'description' => 'required|string',
            'base_url'    => 'required',
            'is_active'   => 'required|boolean'
        ]);

    }
}
