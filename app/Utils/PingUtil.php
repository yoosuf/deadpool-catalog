<?php

namespace App\Utils;

use GuzzleHttp\Client;


class PingUtil
{



    public function __construct()
    {



        $client = new \GuzzleHttp\Client();
        $res = $client->request('GET', 'https://api.github.com/user');
        echo $res->getStatusCode();


        $this->client = new Client([
            'base_uri' => env('CORE_API_PATH'),
            'http_errors' => false
        ]);
    }


    public function get($url)
    {
        $response = $this->client->get($url);
        $body = json_decode($response->getBody()->getContents());
        $statusCode = $response->getStatusCode();
        return compact('body', 'statusCode');
    }

}