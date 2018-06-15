<?php

namespace App\GraphQL\Type;

use GraphQL\Type\Definition\Type;
use Folklore\GraphQL\Support\Type as BaseType;
use GraphQL;

class CountryCurrencyType extends BaseType
{
    protected $attributes = [
        'name' => 'CountryCurrencyType',
        'description' => 'A type'
    ];

    public function fields()
    {
        return [
            'country_id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'The id of the CountryCurrencyType'
            ],
            'currency_id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'The name of CountryCurrencyType'
            ],
        ];
    }
}
