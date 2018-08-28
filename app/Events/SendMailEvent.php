<?php

namespace App\Events;

//use App\Order;
use Illuminate\Queue\SerializesModels;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;

class SendMailEvent extends Event
{
    use SerializesModels;

   // public $response;

    /**
     * Create a new event instance.
     *
     * @param  \App\Order  $order
     * @return void
     */
    public function __construct($response)
    {
      

        echo Storage::disk('local')->url('logins.csv');

        // $contents = Storage::get('logins.csv');
        // echo $contents;
exit;
        //dd($response);
        // $r = $client->request('POST', 'http://httpbin.org/post', [
        //     'body' => $response
        // ]);
    }
}