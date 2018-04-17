<?php

namespace App\Utils;

use Exception;

class GoogleExchange
{

    public function __construct()
    {

    }


    public function convert($fromCurrency, $toCurrency, $amount)
    {
        try {
            $data = file_get_contents("https://finance.google.com/finance/converter?a=$amount&from=$fromCurrency&to=$toCurrency");
            preg_match("/<span class=bld>(.*)<\/span>/", $data, $converted);
            $converted = preg_replace("/[^0-9.]/", "", $converted[1]);
            $data = number_format(round($converted, 3), 2);
            return $data;
        } catch (Exception $error) {
            return $error->getMessage();
        }
    }





}