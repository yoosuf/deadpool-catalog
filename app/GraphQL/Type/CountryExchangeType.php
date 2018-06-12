<?php

namespace App\GraphQL\Type;

use GraphQL\Type\Definition\Type;
use Folklore\GraphQL\Support\Type as BaseType;
use GraphQL;

class CountryExchangeType extends BaseType
{
    protected $attributes = [
        'name' => 'CountryExchangeType',
        'description' => 'A type'
    ];

    public function fields()
    {
        return [

            'country_id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'The id of the CountryExchangeType'
            ],
            'exchange_id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'The name of CountryExchangeType'
            ],
            
        ];
    }
}
