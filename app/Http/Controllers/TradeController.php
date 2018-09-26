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
use App\Trading;
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
            'apiKey' => 'c4c58df9658c25c5377db716dd852070',
            'secret' => 'Uzj7wQUStAq5NvkZ3I+HvBUfNxP2wd11RE1BgE/rnZHe2BUmLkJ9jXqKCv87ndR27+P96HAJLByv/Z0TnpVuXA==',
            'password' => '1ij971b1z3y'
        ));

        // $this->coinbasePro    = new \ccxt\coinbasepro (array (
        //     'apiKey' => 'a2f4dae19e5e1f60f15066ab6287a509',
        //     'secret' => 'IJgnkO6DNwX9cj4P3UnbsZSx6CInvyDHH9g+kb/jQjcpluwiE+3rMB3W1j9d8PRzfWBCDSpzO6psjmHJqrTX+Q==',
        //     'password' => 'b22etfk1ol'
        // ));

        $this->coinbasePro->urls['api'] = "https://api-public.sandbox.pro.coinbase.com";

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

        $configuration = Configuration::apiKey('76x6uMvSwNUqiw0V', 'QShF7l3vNgRAEUOm6QkxPp4LVEMsKWbQ');
        
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

        $amount = 1000;

        $cryptoCanBuy = $amount/$buyLivePrice;
    
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

        $response[] = $buyInfo;
        $response[] = $sellInfo;

       return response()->json([

        'fiats' => $response,
        'profitInfo' => $profitInfo,
        'cryptoCanBuy' => $cryptoCanBuy
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


   
    public function transfer(Request $request)
    {

       // $liveBuyResult = $this->coinbase->fetch_ticker ('BTC/GBP');

       $res = $this->coinbasePro->get_payment_methods();

       print_r($res);
       exit;


        $res = $this->client->getCurrentAuthorization();

        // $accounts = $this->client->getAccounts();

        
        //$account = $this->client->getAccount('e2457285-dfed-5fa6-aab4-18b563d9ce1e');


        print_r($res);exit;

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

       print_r($request['latestBuy']);exit;

        $amount = $request->get('amount');

        $buyCurrncy = $request->get('buy_currency');
        $buyExchange = $request->get('buy_exchanges');
        $buyCrypto = $request->get('buy_crypto');

        $sellCurrncy = $request->get('sell_currency');
        $sellExchange = $request->get('sell_exchanges');
        $sellCrypto = $request->get('sell_crypto');

        //($currency, $amount, $address, $params = array ()) {

        $params = [

            'coinbase_account_id' => 'c7675ab9-c77a-406d-9184-4b6d3c44df24'
        ];

        // $res = $this->coinbasePro->fetch_order('e589b597-3dda-4afc-a951-0c28620de363');
       
        // print_r($res);exit;
    //    $this->coinbasePro->fetch_balance();
       

        $symbol = 'BTC/USD';//$buyCrypto.'/'.$buyCurrncy;

        $tickerRes = $this->coinbasePro->fetch_ticker ($symbol);

        $amount = 100;

        $liveBuyPrice = $tickerRes['ask']; // this should change according to exchange

        $orderRes = $this->coinbasePro->create_order ($symbol, 'market', 'buy', $amount);
       // $orderRes = $this->coinbasePro->create_order ('LTC/USD', 'market', 'sell', 1);


        $fee = 1;

        if(!empty($orderRes))
        {
            $transaction = new Trading;

            $transaction->type = 'buy order';
            $transaction->from_exchange = $buyExchange;
            $transaction->to_exchange = $sellExchange;
            $transaction->from_currency = $buyCurrncy;
            $transaction->to_currency = $sellCurrncy;
            $transaction->from_crypto = $buyCrypto;
            $transaction->to_crypto = $sellCrypto;
            $transaction->start_order_price = $liveBuyPrice;
            $transaction->end_order_price = '';
            $transaction->amount = $amount;
            $transaction->start_response = json_encode($orderRes);
            $transaction->end_response = '';
            $transaction->status = 0;

            $transaction->save();

        }

        return response()->json([
            // 'fee' => $fee,
            // 'cryptoCanBuy' => $cryptoCanBuy,
            // 'liveBuyPrice' => $liveBuyPrice,
            'response' => $orderRes
    ]);


    }

}
