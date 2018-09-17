<?php

namespace App\Http\Controllers;

use ccxt\ccxt;
// use Coinbase\Wallet\Resource\Transaction;
use Coinbase\Wallet\Resource\Account;
use Coinbase\Wallet\Client;
use Coinbase\Wallet\Configuration;
use Coinbase\Wallet\Enum\CurrencyCode;
use Coinbase\Wallet\Value\Money;
use Coinbase\Wallet\Resource\Buy;
use Coinbase\Wallet\Resource\Sell;
// use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Transaction;
use DB;


class TradeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */

    private $coinbasePro;
    private $client;
    private $key;
    private $secret;
    private $passphrase;
    private $coinbase;
    
    

    public function __construct()
    {
        $this->coinbasePro    = new \ccxt\coinbasepro (array (
            'apiKey' => 'a9dc9bc86279373db3280e2618c6e07c',
            'secret' => 'aPMj8Ht+KKdNJlnKsrXe//oe8f/A8f9ynR4cVE2uNZ0fP/VASoW9ngd+GvrxsVR688xnF6EREuxhlLZWyFBDgg==',
            'password' => 'zs7irctmt5s'
        ));

        $this->coinbase    = new \ccxt\coinbase (array (
            'apiKey' => '7ZwQTSfbA8MNHa9F',
            'secret' => 'yswMi2RFI8dAFfPH563VafVDAXpu0ScS',
        ));

        $this->coinbase->markets['BTC/CAD'] = array ( 'id' => 'btc-cad', 'symbol' => 'BTC/CAD', 'base' => 'BTC', 'quote' => 'CAD');
        $this->coinbase->markets['BTC/GBP'] = array ( 'id' => 'btc-gbp', 'symbol' => 'BTC/GBP', 'base' => 'BTC', 'quote' => 'GBP');

        $this->coinbase->markets['ETH/CAD'] = array ( 'id' => 'eth-cad', 'symbol' => 'ETH/CAD', 'base' => 'ETH', 'quote' => 'CAD');
        $this->coinbase->markets['ETH/GBP'] = array ( 'id' => 'eth-gbp', 'symbol' => 'ETH/GBP', 'base' => 'ETH', 'quote' => 'GBP');

        $this->coinbase->markets['LTC/CAD'] = array ( 'id' => 'ltc-cad', 'symbol' => 'LTC/CAD', 'base' => 'LTC', 'quote' => 'CAD');
        $this->coinbase->markets['LTC/GBP'] = array ( 'id' => 'ltc-gbp', 'symbol' => 'LTC/GBP', 'base' => 'LTC', 'quote' => 'GBP');


        //$this->apiUrl = 'https://api-public.sandbox.pro.coinbase.com';

        $configuration = Configuration::apiKey('bWU6NbUrQV9wo1ZA', '343753addef296ec2219dfb70ef26c44');
        
       // $configuration->setApiUrl('https://api-public.sandbox.pro.coinbase.com');

        $this->client = Client::create($configuration);

        //$this->client = new Client();
    }

    public function getLiveData(Request $request)
    {
       $oldBuyData = $request->get('buydata');
       $oldSellData = $request->get('selldata');

       $buyInfo = [];
       $sellInfo = [];

       $oldProfitInfo = [];
       $liveProfitInfo = [];
       

       $buyExchnage = $oldBuyData['name'];
       $buyCurrency = $oldBuyData['currency'];
       $buyCrypto = $oldBuyData['base'];

       $sellExchnage = $oldSellData['name'];
       $sellCurrency = $oldSellData['currency'];
       $sellCrypto = $oldSellData['base'];



       $buyInfo['exchange'] = $buyExchnage;
       $buyInfo['currency'] = $buyCurrency;
       $buyInfo['crypto'] = $buyCrypto;

       $sellInfo['exchange'] = $sellExchnage;
       $sellInfo['currency'] = $sellCurrency;
       $sellInfo['crypto'] = $sellCrypto;

       $oldProfitInfo['buyPrice'] = $oldBuyData['price'];
       $oldProfitInfo['sellPrice'] = $oldSellData['price'];
       $oldProfitInfo['profit'] = $oldSellData['profit'];


       // get coinbase buy live prices

        $buySymbol = $buyCrypto.'/'.$buyCurrency;
        $liveBuyResult = $this->coinbase->fetch_ticker ($buySymbol);
        $buyLivePrice = $liveBuyResult['info']['buy']['data']['amount'];
    
        // get coinbase sell live price

        $sellSymbol = $sellCrypto.'/'.$sellCurrency;
        $liveSellResult = $this->coinbase->fetch_ticker ($sellSymbol);
        $sellLivePrice = $liveSellResult['info']['sell']['data']['amount'];


        $currency_layer = DB::table('currencies')
        ->join('currency_values', 'currencies.id', '=', 'currency_values.currency_id')
        ->select('currencies.iso', 'currency_values.other_conversion_values', 'currency_values.created_at')
        ->latest()
        ->limit(3)
        ->get();


        foreach ($currency_layer as $id => $value) 
        {
            if($value->iso == $buyCurrency)
            {
                $ratesArr = json_decode($value->other_conversion_values);
            }
        }
        $keystr = $buyCurrency.$sellCurrency;
        $rate = $ratesArr->data->$keystr;

        $amount = 1000;

        $val = (floatval($amount) / floatval($buyLivePrice)) * floatval($sellLivePrice);
        $convertedVal = $val/$rate;
        $calculatedVal = $convertedVal - $amount;

        $percentage =  ($calculatedVal/$amount)*100;


        $liveProfitInfo['buyPrice'] = $buyLivePrice;
        $liveProfitInfo['sellPrice'] = $sellLivePrice;
        $liveProfitInfo['profit'] = $percentage;

        $profitInfo[] = $oldProfitInfo;
        $profitInfo[] = $liveProfitInfo;





       return response()->json([

        'buyinfo' => $buyInfo,
        'sellinfo' => $sellInfo,
        'profitInfo' => $profitInfo
       ]);

    //    print_r($tickerRes);
    //    exit;

       
       
    //get live prices

    //    switch ($buyExchnage) {
    //        case 'Coinbase':
    //            # code...
    //            break;
    //         case 'Kraken':
    //         # code...
    //         break;
           
    //        default:
    //            # code...
    //            break;
    //    }



       

    }


   
    public function transfer()
    {

        $liveBuyResult = $this->coinbase->fetch_ticker ('BTC/GBP');


        $res = $this->client->getCurrentAuthorization();

        print_r($liveBuyResult);exit;
        // $account = $this->client->getAccount('e2457285-dfed-5fa6-aab4-18b563d9ce1e');

        // $transactions = $this->client->getAccountTransactions($account);


        

        // $buy = new Sell();

        // $buy->setTotal(new Money(0.001, CurrencyCode::LTC));

        // // $buy = new Buy([
        // //     'bitcoinAmount' => '1',
        // //    // 'currency' => 'USD'
        // // ]);

        // $deposit = $this->client->createAccountSell($account, $buy);

        // $data = $this->client->decodeLastResponse();

       
        // $transaction = Transaction::send([
        //     'toBitcoinAddress' => 'derrain007@gmail.com',
        //     'amount'           => new Money(0.0013, CurrencyCode::LTC),
        //     'description'      => 'Your first ltc!',
        //     'fee'              => '0.0' // only required for transactions under BTC0.0001
        // ]);

        // $res = $this->client->createAccountTransaction($account, $transaction);

        

        print_r($res);exit;
    }

    public function getLiveBuyPrice(Request $request)
    {
        $currncy = $request->get('currency');
        $exchange = $request->get('exchanges');
        $crypto = $request->get('crypto');
        $amount = $request->get('amount');
        $buyprice = $request->get('buyprice');


        //($currency, $amount, $address, $params = array ()) {

        $params = [

            'coinbase_account_id' => 'c7675ab9-c77a-406d-9184-4b6d3c44df24'
        ];

        $res = $this->coinbasePro->fetch_order('8d000ee8-cb9e-4f25-a40c-ff8c5a448834');
       

    //    $this->coinbasePro->fetch_balance();
        print_r($res);exit;

        $symbol = $crypto.'/'.$currncy;

        $tickerRes = $this->coinbasePro->fetch_ticker ($symbol);


        // print_r($tickerRes);exit;

        $liveBuyPrice = $tickerRes['bid']; // this should change according to exchange

        $orderRes = $this->coinbasePro->create_order ($symbol, 'market', 'buy', $liveBuyPrice);
       // $orderRes = $this->coinbasePro->create_order ('LTC/USD', 'market', 'sell', 1);


    //    print_r($orderRes);exit;

        $cryptoCanBuy = $amount/$liveBuyPrice;

        $fee = 1;

        $transaction = new Transaction;

        $transaction->type = 'buy order';
        $transaction->from_exchange = $exchange;
        $transaction->to_exchange = $exchange;
        $transaction->from_currency = $currncy;
        $transaction->to_currency = $currncy;
        $transaction->price = $liveBuyPrice;
        $transaction->amount = $amount;
        $transaction->response = json_encode($orderRes);
        $transaction->status = 2;

        $transaction->save();



        return response()->json([
            // 'fee' => $fee,
            // 'cryptoCanBuy' => $cryptoCanBuy,
            // 'liveBuyPrice' => $liveBuyPrice,
            'response' => $orderRes
    ]);


    }

}
