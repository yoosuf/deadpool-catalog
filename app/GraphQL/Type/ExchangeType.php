<?php

namespace App\GraphQL\Type;

use GraphQL\Type\Definition\Type;
use Folklore\GraphQL\Support\Type as BaseType;
use GraphQL;

class ExchangeType extends BaseType
{
    protected $attributes = [
        'name' => 'ExchangeType',
        'description' => 'A type'
    ];

    public function fields()
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'The id of the exchange'
            ],
            'name' => [
                'type' => Type::string(),
                'description' => 'The name of exchange'
            ],
            'description' => [
                'type' => Type::string(),
                'description' => 'The description of exchange'
            ],
            'is_active' => [
                'type' => Type::string(),
                'description' => 'The is_active of exchange'
            ]
        ];
    }
}
