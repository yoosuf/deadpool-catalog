<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class StatusController extends Controller
{
    protected $client;
    public function __construct()
    {
        $this->client = new Client([
            'http_errors' => false,     // for not throwing exception
            'verify'      => false,     // for not verifying ssl certificate
        ]);
    }



    public function getPing()
    {
        return response()->json([
            'error'   => false,
            'message' => 'OK',
            'time'    => Carbon::now()->toDateTimeString()
        ]);
    }

}
