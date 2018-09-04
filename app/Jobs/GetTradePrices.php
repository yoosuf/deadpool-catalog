<?php

namespace App\Jobs;

use DB;
use ccxt\ccxt;


class ProcessExchanges extends Job
{
    /**
     * Create a new job instance.
     *
     * @return void
     */

    private $coinbasePro;
    
    public function __construct()
    {
        $this->coinbasePro    = new \ccxt\coinbasepro (array (
            'apiKey' => 'a9dc9bc86279373db3280e2618c6e07c',
            'secret' => 'aPMj8Ht+KKdNJlnKsrXe//oe8f/A8f9ynR4cVE2uNZ0fP/VASoW9ngd+GvrxsVR688xnF6EREuxhlLZWyFBDgg==',
            'password' => 'zs7irctmt5s'
        ));
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        

    }

    public function failed(Exception $exception)
    {
        // Send user notification of failure, etc...
    }
}
