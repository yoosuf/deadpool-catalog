<?php

namespace App;

use Coinbase\Wallet\Client;
use Coinbase\Wallet\Configuration;

class Coinbase
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    
    public static function config()
    {
        $apiKey = '7ZwQTSfbA8MNHa9F';
        $apiSecret = 'yswMi2RFI8dAFfPH563VafVDAXpu0ScS';

        $configuration = Configuration::apiKey($apiKey, $apiSecret);
        $client = Client::create($configuration);

        return $client;
    }

}