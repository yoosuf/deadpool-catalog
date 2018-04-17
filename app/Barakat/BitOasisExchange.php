<?php

namespace App\Barakat;


class BitOasisExchange extends Barakat
{
    public $name = "Bitoasis";

    public function getLatestTrade()
    {
        $response = $this->client->get('https://api.bitoasis.net/v1/exchange/ticker/BTC-AED');
        $body = json_decode($response->getBody()->getContents());
        $latest_price = (float) $body->ticker->last_price;
        $response->getStatusCode();
        return $latest_price;
    }



    public function getLatestAskRate()
    {
        $response = $this->client->get('https://api.bitoasis.net/v1/exchange/ticker/BTC-AED');
        $body = json_decode($response->getBody()->getContents());
        $latest_price =  (float) $body->ticker->ask;
        $response->getStatusCode();
        return $latest_price;
    }


}