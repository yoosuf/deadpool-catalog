<?php

namespace App\Barakat;

class CexExchange extends Barakat
{
    public $name = "Cex";

    public function getLatestTrade()
    {
        $response = $this->client->get('https://cex.io/api/last_price/BTC/GBP');
        $body = json_decode($response->getBody()->getContents());
        $latest_price = (float) $body->lprice;
        $response->getStatusCode();
        return $latest_price;
    }



    public function getLatestAskRate()
    {
        $response = $this->client->get('https://cex.io/api/ticker/BTC/GBP');
        $body = json_decode($response->getBody()->getContents());
        $latest_price =  (float) $body->ask;
        $response->getStatusCode();
        return $latest_price;
    }


}