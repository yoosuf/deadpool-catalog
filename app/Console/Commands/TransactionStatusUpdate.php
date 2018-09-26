<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Http\Response;
use Carbon\Carbon;
use ccxt\ccxt;
use Log;
use DB;
use App\Trading;


class TransactionStatusUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'TransactionStatusUpdate:transactionStatusUpdate';

    /**
     * @var
     */
    protected $console;

    /**
     * @var current book
     */
  
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command export csv';

    /**
     * Create a new command instance.
     *
     * @return void
     */

    private $coinbasePro;
    private $coinbase;
    private $transaction;

    public function __construct()
    {
        parent::__construct();

        $this->transaction = new Trading;
        
        $this->coinbasePro    = new \ccxt\coinbasepro (array (
            'apiKey' => 'c4c58df9658c25c5377db716dd852070',
            'secret' => 'Uzj7wQUStAq5NvkZ3I+HvBUfNxP2wd11RE1BgE/rnZHe2BUmLkJ9jXqKCv87ndR27+P96HAJLByv/Z0TnpVuXA==',
            'password' => '1ij971b1z3y'
        ));

        $this->coinbasePro->urls['api'] = "https://api-public.sandbox.pro.coinbase.com";

        $this->coinbase    = new \ccxt\coinbase (array (
            'apiKey' => '7ZwQTSfbA8MNHa9F',
            'secret' => 'yswMi2RFI8dAFfPH563VafVDAXpu0ScS',
        ));
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $params = [

            'coinbase_account_id' => 'bcdd4c40-df40-5d76-810c-74aab722b223'
        ];

        //$res = $this->coinbasePro->deposit('USD', 100, '', $params);
        
      
        $transactionRes = $this->transaction::where('status', 0)
                            ->orderBy('id', 'desc')
                            ->get();

        foreach ($transactionRes as $key => $value) {

            $prevStatus = $value->status;
            $symbol = $value->from_crypto.'/'.$value->from_currency;
            $orderes = json_decode($value->start_response);
            $orderid = $orderes->id;
            $fromExchange = $value->from_exchange;
            $toExchange = $value->to_exchange;

            
            $orderRes = $this->coinbasePro->fetch_order($orderid);

            $newStatus = $this->getStatus ($orderRes['info']['status']);

            
            
            if($value->type == 'buy order' AND $prevStatus != $newStatus){

                    $filledCryptoAmt = (float)$orderRes['info']['filled_size'];

                    // $filledCryptoAmt = round ($filledCryptoAmt, 2);

                    $trans = $this->transaction::find($value->id);

                    $statusRes = $this->updateStatus($trans, $orderRes);

                    if($statusRes AND $fromExchange == $toExchange){

                        $tickerRes = $this->coinbasePro->fetch_ticker ($symbol);

                        $sellOrderRes = $this->coinbasePro->create_order ($symbol, 'market', 'sell', $filledCryptoAmt);
                        // print_r($sellOrderRes);exit;
                       // float
                        if(!empty($sellOrderRes))
                        {
                            $this->saveSellOrder($trans, $sellOrderRes, $tickerRes, $filledCryptoAmt);
                        }
                        
                        // print_r($sellOrderRes);exit;
                    }


            } else if($value->type == 'sell-transfer' AND $prevStatus != $newStatus){

              
                print_r($orderRes);exit; 

            }
        }
        
    }

    public function updateStatus($trans, $orderRes)
    {
        $trans->status = 1;
        $trans->end_response = json_encode($orderRes);
        
        return $trans->save();
    }

    public function saveSellOrder($orderArr, $sellOrderRes, $livePrice, $amount) {

            $transaction = new Trading;

            $transaction->type = 'sell-transfer';
            $transaction->from_exchange = $orderArr->from_exchange;
            $transaction->to_exchange = $orderArr->to_exchange;
            $transaction->from_currency = $orderArr->from_currency;
            $transaction->to_currency = $orderArr->to_currency;
            $transaction->from_crypto = $orderArr->from_crypto;
            $transaction->to_crypto = $orderArr->to_crypto;
            $transaction->start_order_price = $livePrice['bid'];
            $transaction->end_order_price = '';
            $transaction->amount = $amount;
            $transaction->start_response = json_encode($sellOrderRes);
            $transaction->end_response = '';
            $transaction->status = 0;

            $transaction->save();

    }

    public function getStatus($name)
    {   
        $status = '';

        switch ($name) {

            case 'done':
                $status = 1;
                break;
            
            case 'pending':
                $status = 0;
                break;
        }

        return $status;
    }
}