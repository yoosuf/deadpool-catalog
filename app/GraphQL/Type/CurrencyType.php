<?php

namespace App\GraphQL\Type;

use GraphQL\Type\Definition\Type;
use Folklore\GraphQL\Support\Type as BaseType;
use GraphQL;

class CurrencyType extends BaseType
{
    protected $attributes = [
        'name' => 'CurrencyType',
        'description' => 'A type'
    ];

    public function fields()
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'The id of the Currency'
            ],
            'name' => [
                'type' => Type::string(),
                'description' => 'The name of Currency'
            ],
            'iso' => [
                'type' => Type::string(),
                'description' => 'The iso of Currency'
            ],
            'iso3' => [
                'type' => Type::string(),
                'description' => 'The iso3 of Currency'
            ],
            'symbol' => [
                'type' => Type::string(),
                'description' => 'The symbol of Currency'
            ],
            'preference' => [
                'type' => Type::string(),
                'description' => 'The preference of Currency'
            ]
        ];
    }
}
