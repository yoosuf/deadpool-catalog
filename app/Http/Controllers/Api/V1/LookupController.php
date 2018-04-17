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

class LookupController extends ApiController
{
    private $exchange;
    private $country;
    private $currency;
    private $crypt;
    private $googleExchange;

    /**
     * Create a new controller instance.
     * @param Exchange $exchange
     * @param Country $country
     * @param Currency $currency
     * @param Crypt $crypt
     * @param GoogleExchange $googleExchange
     */
    public function __construct(Exchange $exchange, Country $country, Currency $currency, Crypt $crypt, GoogleExchange $googleExchange)
    {
        $this->exchange = $exchange;
        $this->country = $country;
        $this->currency = $currency;
        $this->crypt = $crypt;

        $this->googleExchange = $googleExchange;
    }


    /**
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Collection|mixed|static[]
     */
    public function index(Request $request)
    {
        $this->validate($request, [
            'from' => 'required|exists:currencies,code',
            'to' => 'required|exists:currencies,code',
            'amount' => 'required'
        ]);

        $fromCurrency = $request->has('from') ? $request->get('from') : null;
        $toCurrency = $request->has('to') ? $request->get('to') : null;
        $amount = $request->has('amount') ? $request->get('amount') : null;

        $googleRaw = $this->googleExchange->convert($fromCurrency, $toCurrency, $amount);
        $google = floatval(str_replace(',', '', $googleRaw));

        $fromCurrencyId = $this->currency->where('code', $fromCurrency)->first();

        $toCurrencyId = $this->currency->where('code', $toCurrency)->first();

        // dd($toCurrencyId);

        if ($fromCurrency) {
            $exchange['from'] = $this->exchange->with(['country', 'currency', 'crypts'])->where('currency_id', '=', $fromCurrencyId->id)->get();
        }

        if ($toCurrency) {
            $exchange['to'] = $this->exchange->with(['country', 'currency', 'crypts'])->where('currency_id', '=', $toCurrencyId->id)->get();
        }

        $from_array = [];
        foreach ($exchange['from']->toArray() as $item) {
            $from_array[] = $this->renderExchangeData($item, 'from', $amount);
        }

        $to_array = [];
        foreach ($exchange['to']->toArray() as $item) {
            $to_array[] = $this->renderExchangeData($item, 'to', $amount);
        }

        $demo1 = array_values($from_array);
        $demo2 = array_values($to_array);

        $exchanges = array_merge($demo1, $demo2);

        // dd($traders);


        $collection = collect([
                "google" => $google,
                "data" => $exchanges,
            ]
        );

        return $collection;
    }


    private function renderExchangeData($data, $type, $amount)
    {
        return [
            "id" => $data['id'],
            "name" => $data['name'],
            "is_active" => $data['is_active'],
            "updated_at" => $data['updated_at'],
            "country" => $data['country']['iso3'],
            "currency_code" => $data['currency']['code'],
            "crypto_currencies" => $this->renderCryptoCurrency($data['crypts'], $type, $amount),
            "charge_options" => $this->renderOptions($data['preferences'], $amount),
        ];
    }

    private function renderCryptoCurrency($data, $type, $amount)
    {
        $currency_list = [];
        foreach ($data as $crypt) {

            if($type == 'from')
                $current_rate = $crypt['pivot']['current_rate'];
            if($type == 'to')
                $current_rate = $crypt['pivot']['asking_rate'];


            $preferences = json_decode($crypt['pivot']['preferences'], TRUE);
            // dd($preferences);
                $currency_list[] = [
                    "type" => $crypt['code'],
                    "charges" => $this->renderCharges($preferences, $amount),
                    "current_rate" => isset($current_rate) ? $current_rate : "0",
                    "updated_at" => $crypt['pivot']['updated_at']      
                ];
        }

        if (!empty($currency_list)) {
            return $currency_list;
        } else {
            return json_decode("{}");
        }
    }

    private function renderOptions($data, $amount)
    {
        $option_list = [];
        foreach ($data as $option) {
                $option_list[] = [
                    "name" => isset($option['name']) ? $option['name'] : "",
                    "is_active" => isset($option['is_active']) ? $option['is_active'] : false,
                    "charges" => $this->renderCharges($option, $amount)     
                ];
        }

        if (!empty($option_list)) {
            return $option_list;
        } else {
            return json_decode("{}");
        }
    }

    private function renderCharges($data, $amount)
    {
        // dd($data['fees']);
        $charge_list = [];
        foreach ($data['fees'] as $charge) {
                $charge_list[] = [
                    "name" => isset($charge['name']) ? $charge['name'] : "",
                    "is_active" => isset($charge['is_active']) ? $charge['is_active'] : false,
                    "deduct_stage" => isset($charge['stage']) ? $charge['stage'] : "",
                    "options" => $this->renderChargeOptions($charge['options'], $amount)     
                ];
        }

        if (!empty($charge_list)) {
            return $charge_list;
        } else {
            return json_decode("{}");
        }
    }

    private function renderChargeOptions($data, $amount)
    {
        $charge_option_list = [];
        foreach ($data as $charge_option) {
            // dd($charge_option['fee_range']['min']);  
            $is_active = isset($charge_option['is_active']) ? $charge_option['is_active'] : false;
            if(isset($charge_option['fee_range']) && $charge_option['fee_range']['is_active'])
            {
                if($charge_option['fee_range']['min'] > $amount || $charge_option['fee_range']['max'] < $amount)
                {
                    $is_active = false;
                }
            }
                $charge_option_list[] = [
                    "is_percentage" => isset($charge_option['is_percentage']) ? $charge_option['is_percentage'] : false,
                    "is_active" => $is_active,
                    "type" => isset($charge_option['type']) ? $charge_option['type'] : "",
                    "value" => isset($charge_option['value']) ? $charge_option['value'] : 0,   
                ];
        }

        if (!empty($charge_option_list)) {
            return $charge_option_list;
        } else {
            return json_decode("{}");
        }
    }

    // private function renderCharges($data)
    // {
    //     return [
    //         'payment_gateway_fee' => isset($data['charges']['payment_gateway_fee']) ? $data['charges']['payment_gateway_fee'] : "0",
    //         'barakat_fee' => isset($data['charges']['barakat_fee']) ? $data['charges']['barakat_fee'] : "0",
    //         'otp_fee' => isset($data['charges']['otp_fee']) ? $data['charges']['otp_fee'] : "0",
    //         'bank_tranfer_fee' => isset($data['charges']['bank_tranfer_fee']) ? $data['charges']['bank_tranfer_fee'] : "0",
    //     ];
    // }


}

