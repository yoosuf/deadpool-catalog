<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/




$headers = [
    'headers' => [
        'Accept' => 'application/json',
        'Content-Type' => 'application/json',
        'api-version' => '1',
    ]
];


$router->get('/', function () {
    return response()->json(["error" => ["message" => "hey"]]);
});


// $router->get('/dumper', 'Api\V1\DataDumpController@dumper');



$router->get('api/ping', 'Api\StatusController@getPing');



$router->group($headers, function () use ($router) {


    $router->group(['namespace' => 'Api\V1', 'prefix' => 'api/v1'], function () use ($router) {


        $router->get('/lookup', 'LookupController@index');

        $router->get('/providers', 'ProvidersController@index');



        $router->group(['prefix' => 'currencies'], function () use ($router) {
            $router->get('/', 'CurrenciesController@index');
            $router->post('/', 'CurrenciesController@save');
            $router->get('/{id}', 'CurrenciesController@show');
            $router->patch('/{id}', 'CurrenciesController@update');
        });

        $router->group(['prefix' => 'countries'], function () use ($router) {
            $router->get('/', 'CountriesController@index');
            $router->post('/', 'CountriesController@save');
            $router->get('/{id}', 'CountriesController@show');
            $router->patch('/{id}', 'CountriesController@update');
        });

        $router->group(['prefix' => 'cryptos'], function () use ($router) {
            $router->get('/', 'CryptsController@index');
            $router->post('/', 'CryptsController@save');
            $router->get('/{id}', 'CryptsController@show');
            $router->patch('/{id}', 'CryptsController@update');
        });


        $router->group(['prefix' => 'exchanges'], function () use ($router) {
            $router->get('/', 'ExchangesController@index');
            $router->post('/', 'ExchangesController@save');
            $router->get('/{id}', 'ExchangesController@show');
            $router->patch('/{id}', 'ExchangesController@update');
        });

    });

});
