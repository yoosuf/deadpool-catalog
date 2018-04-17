<?php

namespace App\Barakat;

use GuzzleHttp\Client;

class Barakat
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => '',
            "headers" => [
                "accept" => "application/json",
            ],
            'http_errors' => false
        ]);
    }
}