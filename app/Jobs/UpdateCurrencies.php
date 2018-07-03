<?php

namespace App\Jobs;

use DB;
use App\CurrencyLog;
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
        $apiKey = 'access_key=1b48ca80e794b1efaedb364f3834957c';

        $url = 'https://apilayer.net/api/live?'.$apiKey;

        $client = new Client();
        $res = $client->get($url);

        $response = json_decode($res->getBody());

        $encodeResponse = json_encode($response);

        $data = [
            'preference' => $encodeResponse
        ];

        $newData = CurrencyLog::create($data);
    }

    public function failed(Exception $exception)
    {
        // Send user notification of failure, etc...
    }
}
