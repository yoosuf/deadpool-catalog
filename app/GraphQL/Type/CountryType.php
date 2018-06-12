<?php

namespace App\GraphQL\Type;

use GraphQL\Type\Definition\Type;
use Folklore\GraphQL\Support\Type as BaseType;
use GraphQL;

class CountryType extends BaseType
{
    protected $attributes = [
        'name' => 'CountryType',
        'description' => 'A type'
    ];

    public function fields()
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'The id of the country'
            ],
            'name' => [
                'type' => Type::string(),
                'description' => 'The name of country'
            ],
            'nice_name' => [
                'type' => Type::string(),
                'description' => 'The nice_name of country'
            ],
            'iso' => [
                'type' => Type::string(),
                'description' => 'The iso of country'
            ],
            'iso3' => [
                'type' => Type::string(),
                'description' => 'The iso3 of country'
            ],
            'phone_code' => [
                'type' => Type::string(),
                'description' => 'The phone_code of country'
            ],
            'preference' => [
                'type' => Type::string(),
                'description' => 'The preference of country'
            ],
            'exchanges' => [
                'type' => Type::listOf(GraphQL::type('ExchangeType')),
                'description' => 'The relation details'
            ],
            'currencies' => [
                'type' => Type::listOf(GraphQL::type('CurrencyType')),
                'description' => 'The relation details'
            ],
        ];
    }
}
