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

    $quoteList = [
        "Jealousy is a dog's bark which attracts thieves. - Karl Kraus", 
        "The great thieves lead away the little thief. - Diogenes", 
        "Real knowledge is to know the extent of one's ignorance. - Confucius", 
        "The true sign of intelligence is not knowledge but imagination. - Albert Einstein", 
        "Work like you don't need the money. Love like you've never been hurt. Dance like nobody's watching. - Satchel Paige", 
        "Making money is art and working is art and good business is the best art. - Andy Warhol"
    ];

    return response()->json(["quote" => $quoteList[mt_rand(0, count($quoteList)-1)]], 200);

    // return $router->app->version();
});

$router->get('calculate', 'FIltersController@calculateData');

$router->group(['namespace' => 'Api\V1', 'prefix' => 'v1'], function ($router) {

    $router->get('calculate', 'FiltersController@calculateData');

    $router->get('countries', 'CountriesController@index');
    $router->get('countries/{countryId}', 'CountriesController@show');

    $router->get('currencies', 'CurrenciesController@index');
    $router->get('currencies/{currencyId}', 'CurrenciesController@show');

    $router->get('cryptos', 'CryptosController@index');
    $router->get('cryptos/{cryptoId}', 'CryptosController@show');
    
    $router->get('exchanges', 'ExchangesController@index');
    $router->get('exchanges/{exchangeId}', 'ExchangesController@show');

    $router->get('exchanges/{exchangeId}/logs', 'ExchangeLogsController@index');
    $router->get('exchanges/{exchangeId}/logs/{logId}', 'ExchangeLogsController@show');
});
