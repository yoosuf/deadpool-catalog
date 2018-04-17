<?php

namespace App\Http\Controllers\Api\V1;

use App\Entities\Country;
use App\Entities\Crypt;
use App\Entities\Currency;
use App\Entities\Exchange;
use App\Entities\User;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;

class DataDumpController extends ApiController
{

    /**
     * Create a new controller instance.
     *
     */
    public function __construct()
    {
    }

    /**
     */
    public function dumper()
    {

        $appUsers = [
            [
                'name' => 'Barkat Admin'
            ],
            [
                'name' => 'Barkat Rice'
            ],
            [
                'name' => 'Barkat Frontend'
            ]
        ];

//        DB::table('users')->truncate();
        foreach ($appUsers as $user) {
            User::create([
                'name' => $user['name']
            ]);
        }

        $country_path = realpath(__DIR__ . '/../../../../../storage/data/countries.json');
        $country_json = json_decode(file_get_contents($country_path), true);

//        DB::table('countries')->truncate();
        foreach ($country_json as $obj) {
            Country::create([
                'name' => $obj['name'],
                'nice_name' => $obj['nice_name'],
                'iso' => $obj['iso'],
                'iso3' => $obj['iso3'],
                'phone_code' => $obj['phonecode'],
                'is_active' => true
            ]);
        }

        $crypt_path = realpath(__DIR__ . '/../../../../../storage/data/crypts.json');
        $crypt_json = json_decode(file_get_contents($crypt_path), true);

        foreach ($crypt_json as $obj) {
            Crypt::create([
                'name' => $obj['name'],
                'code' => $obj['code'],
                'symbol' => $obj['symbol'],
                'is_active' => true
            ]);
        }


        $currency_path = realpath(__DIR__ . '/../../../../../storage/data/currencies.json');
        $currency_json = json_decode(file_get_contents($currency_path), true);
        foreach ($currency_json as $obj) {
            Currency::create([
                'name' => $obj['name'],
                'code' => $obj['code'],
                'symbol' => $obj['symbol'],
                'is_active' => true
            ]);
        }

    }
}
