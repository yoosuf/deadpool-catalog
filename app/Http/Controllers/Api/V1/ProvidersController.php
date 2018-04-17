<?php


namespace App\Http\Controllers\Api\V1;

use App\Barakat\BitOasisExchange;
use App\Barakat\CexExchange;
use App\Barakat\CoinFloorExchange;
use App\Barakat\ConSecureExchange;
use App\Entities\Country;
use App\Entities\Crypt;
use App\Entities\Currency;
use App\Entities\Exchange;


use App\Utils\GoogleExchange;


use App\Http\Controllers\Api\ApiController;
use Illuminate\Http\Request;

use App\Http\Resources\Exchange as ExchangeResource;


class ProvidersController extends ApiController
{
    private $exchange;
    private $country;
    private $currency;
    private $crypt;
    private $conSecureExchange;
    private $cexExchange;
    private $coinFloorExchange;
    private $bitOasisExchange;
    private $googleExchange;

    /**
     * Create a new controller instance.
     * @param Exchange $exchange
     * @param Country $country
     * @param Currency $currency
     * @param Crypt $crypt
     */
    public function __construct(Exchange $exchange, Country $country, Currency $currency, Crypt $crypt,
                                ConSecureExchange $conSecureExchange, CexExchange $cexExchange,
                                CoinFloorExchange $coinFloorExchange, BitOasisExchange $bitOasisExchange, 
                                GoogleExchange $googleExchange)
    {
        $this->exchange = $exchange;
        $this->country = $country;
        $this->currency = $currency;
        $this->crypt = $crypt;
        $this->cexExchange = $cexExchange;
        $this->googleExchange  = $googleExchange;
        $this->bitOasisExchange  =$bitOasisExchange;
        $this->conSecureExchange = $conSecureExchange;
        $this->coinFloorExchange  = $coinFloorExchange;

    }


    public function index() {

        $data = [
            "latest_trade" => [
                "bitoasis" => $this->bitOasisExchange->getLatestTrade(),
                "cex" => $this->cexExchange->getLatestTrade(),
                "coinsecure" => $this->conSecureExchange->getLatestTrade(),
                "coinfloor" => $this->coinFloorExchange->getLatestTrade(),
            ],
           "ask_rate" => [
                "bitoasis" => $this->bitOasisExchange->getLatestAskRate(),
                "cex" => $this->cexExchange->getLatestAskRate(),
                "coinfloor" => $this->coinFloorExchange->getLatestAskRate(),
                "coinsecure" => $this->conSecureExchange->getLatestAskRate(),
                
            ]
        ];

        return $data;
    }

}