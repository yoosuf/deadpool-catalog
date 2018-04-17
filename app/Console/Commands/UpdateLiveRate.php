<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;
use Illuminate\Console\Command;
use App\Entities\Exchange;
use Illuminate\Support\Facades\DB;
use App\Barakat\BitOasisExchange;
use App\Barakat\CexExchange;
use App\Barakat\CoinFloorExchange;
use App\Barakat\ConSecureExchange;

class UpdateLiveRate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'barakat:catalog-update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updating catalog';
    private $exchange;
    protected $client;
    private $exchanger_list = array();

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Exchange $exchange)
    {
        parent::__construct();
        $this->exchange = $exchange;

        $this->client = new Client([
            'http_errors' => false,
            'verify'      => false,
            'timeout' => 3.14
        ]);
        array_push($this->exchanger_list, new BitOasisExchange());
        array_push($this->exchanger_list, new CexExchange());
        array_push($this->exchanger_list, new CoinFloorExchange());
        array_push($this->exchanger_list, new ConSecureExchange());
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        // $this->info('it works!');

        // $this->pingExchanges('https://barkat-catalog.herokuapp.com/api/v1/currencies');

        $this->handleExchanges();

        // DB::table('users')->delete(4);
    }


    /**
     *
     * Father of for all exchanges
     * Ping services, and get the list of active services
     * Then get the buying and selling of each service via respective their t8ket feed
     * Then Feed them to DB
     */
    private function handleExchanges()
    {
        $exchangers_entities = $this->exchange->get();
        foreach ($exchangers_entities as $exchanger_entitiy)
        {
            foreach($this->exchanger_list as $exchanger)
            {

                if($exchanger_entitiy->name == $exchanger->name)
                {
                    $this->info("Name : " . $exchanger_entitiy->name);
                    $crypto = $exchanger_entitiy->crypts()->where('code','BTC')->first();

                    $data = [
                        'current_rate' => $exchanger->getLatestTrade(), 
                        'asking_rate' => $exchanger->getLatestAskRate()
                    ];

                    $exchanger_entitiy->crypts()->updateExistingPivot($crypto->id, $data);
                }
            }
        }
    }


    private function pingExchanges($data, $exchange)
    {
        $res = $this->client->get($exchange);
        $status = ($res->getStatusCode() === 200) ? "true" : "false";

        $exchange = $this->exchange->find($data->id);

//        $exchange->crypts()->updateExistingPivot($roleId, $attributes);

//        DB::table('exchanges')
//            ->where('id', $data->id)
//            ->update(['votes' => $status]);

    }
}