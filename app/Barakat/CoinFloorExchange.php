<?php

namespace App\Barakat;


class CoinFloorExchange extends Barakat
{
    public $name = "Coinfloor";

    public function getLatestTrade()
    {
        $response = $this->client->get('https://webapi.coinfloor.co.uk:8090/bist/XBT/GBP/ticker/');
        $body = json_decode($response->getBody()->getContents());
        $latest_price =  (float) $body->last;
        $response->getStatusCode();
        return $latest_price;
    }





    public function getLatestAskRate()
    {
        $response = $this->client->get('https://webapi.coinfloor.co.uk:8090/bist/XBT/GBP/ticker/');
        $body = json_decode($response->getBody()->getContents());
        $latest_price =  (float) $body->ask;
        $response->getStatusCode();
        return $latest_price;
    }



}