<?php

namespace App\Http\Controllers;

use ccxt\ccxt;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

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
        //$this->client = new Client();
    }


    public function getLatestPrice(){

        $params = [

            'coinbase_account_id' => '2978aa28-9d5e-47e9-aca6-a69418cb1b09'
        ];

        // //print_r($params);exit;
        $path = "v2/accounts/b817f8aa-3827-5bc1-bb84-f64a8368b209/addresses";



        // public function sign ($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {
            // $request = '/' . $this->implode_params($path, $params);
            // $query = $this->omit ($params, $this->extract_params($path));

        $coinbaseResult = $this->coinbasePro->sign ($path, 'private', '', $params);

        // // ($path, $api = 'public', $method = 'GET', $params = array (), $headers = null, $body = null) {

       // print_r($coinbaseResult);exit;
        // //$buyPrice = $coinbaseResult['info']['buy']['data']['amount'];

        $agent =   'Mozilla/5.0 (X11; Linux x86_64)';

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $coinbaseResult['url']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $coinbaseResult['body']);
        curl_setopt($ch, CURLOPT_POST, 1);
       curl_setopt($ch, CURLOPT_USERAGENT, $agent);

        curl_setopt($ch, CURLOPT_HTTPHEADER,$coinbaseResult['headers']);

       
        $result = curl_exec($ch);

        print_r($result);exit;

        
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }
        curl_close ($ch);
       

        
    }

    public function getLiveBuyPrice(Request $request)
    {
        $currncy = $request->get('currency');
        $exchange = $request->get('exchanges');
        $crypto = $request->get('crypto');
        $amount = $request->get('amount');
        $buyprice = $request->get('buyprice');



        $coinbaseResult = $this->coinbase->fetch_ticker ($crypto.'/'.$currncy);

        $liveBuyPrice = $coinbaseResult['info']['buy']['data']['amount'];

        $cryptoCanBuy = $amount/$liveBuyPrice;

        $fee = 1;



        return response()->json([
            'fee' => $fee,
            'cryptoCanBuy' => $cryptoCanBuy,
            'liveBuyPrice' => $liveBuyPrice
    ]);

        //print_r($coinbaseResult);exit;


        // ob_implicit_flush();

        // $address = 'wss://ws-feed.pro.coinbase.com';
        // //$port = 10000;
        
        // $server = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        // socket_set_option($server, SOL_SOCKET, SO_REUSEADDR, 1);
        // socket_bind($server, $address);
        // socket_listen($server);
        // $client = socket_accept($server);

        // $params = [
        //     "type"=>"subscribe",
        //     "product_id"=> "BTC-USD"
        // ];

        // $json = json_encode($params);

        // $length = strlen($json);

    
        // socket_write($socket, $json, $length);

        // $response = socket_read($client, 5000);

        // print_r($response);
        
       
        
        // socket_close($sock);



    }

}
