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

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->get('currencies', 'FIltersController@getCurrencies');

$router->get('calculate', 'FIltersController@calculateData');






$router->group(['namespace' => 'Api\V1', 'prefix' => 'v1'], function ($router) {

    $router->get('/countries', 'CountriesController@index');
    $router->get('/countries/{countryId}', 'CountriesController@show');

    $router->get('/currencies', 'CurrenciesController@index');
    $router->get('/currencies/{currencyId}', 'CurrenciesController@show');

    $router->get('/cryptos', 'CryptosController@index');
    $router->get('/cryptos/{cryptoId}', 'CryptosController@show');
    
    $router->get('/exchanges', 'ExchangesController@index');
    $router->get('/exchanges/{exchangeId}', 'ExchangesController@show');

    $router->get('/exchanges/{exchangeId}/logs', 'ExchangeLogsController@index');
    $router->get('/exchanges/{exchangeId}/logs/{logId}', 'ExchangeLogsController@show');
});
