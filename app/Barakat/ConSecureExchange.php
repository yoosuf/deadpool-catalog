<?php

namespace App\Barakat;


class ConSecureExchange extends Barakat
{
    public $name = "Coinsecure";

    public function getLatestTrade()
    {
        $response = $this->client->get('https://api.coinsecure.in/v1/exchange/lastTrade');
        $body = json_decode($response->getBody()->getContents());
        $latest_price =  $body->message->avgRate;
        $response->getStatusCode();
        return $latest_price/100;
    }


    public function getLatestAskRate()
    {
        $response = $this->client->get('https://api.coinsecure.in/v1/exchange/ticker');
        $body = json_decode($response->getBody()->getContents());
        $latest_price =  $body->message->ask;
        $response->getStatusCode();
        return $latest_price/100;
    }
}