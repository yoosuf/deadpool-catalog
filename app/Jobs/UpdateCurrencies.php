<?php

namespace App\Jobs;

use DB;
use App\CurrencyValue;
use GuzzleHttp\Client;


class UpdateCurrencies extends Job
{
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $currencyArr = DB::table('currencies')
        ->whereIn('iso', ['USD', 'CAD', 'GBP'])
        ->pluck('iso', 'id');

        $currencyStr = '&currencies=USD,CAD,GBP';

        $apiKey = 'access_key=1b48ca80e794b1efaedb364f3834957c';
       

       // https://apilayer.net/api/live?access_key=1b48ca80e794b1efaedb364f3834957c&source=GBP&currencies=AUD,CHF,EUR,GBP,PLN&format=1

        foreach ($currencyArr as $key => $value)
        {
            $finalArr = [];
            $source = '&source='.$value;

            $url = 'https://apilayer.net/api/live?'.$apiKey.$source.$currencyStr.'&format=1';

            
            $client = new Client();
            $res = $client->get($url);
    
            $response = json_decode($res->getBody());

            $finalArr = array(
                'source' => $response->source,
                'data' => $response->quotes
            );

            $encodeResponse = json_encode($finalArr);

            $data = [
                'currency_id' => $key,
                'other_conversion_values' => $encodeResponse
            ];

            $newData = CurrencyValue::create($data);
        }
    }

    public function failed(Exception $exception)
    {
        // Send user notification of failure, etc...
    }
}
