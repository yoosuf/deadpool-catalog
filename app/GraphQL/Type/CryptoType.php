<?php

namespace App\GraphQL\Type;

use GraphQL\Type\Definition\Type;
use Folklore\GraphQL\Support\Type as BaseType;
use GraphQL;

class CryptoType extends BaseType
{
    protected $attributes = [
        'name' => 'CryptoType',
        'description' => 'A type'
    ];

    public function fields()
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'The id of the Crypto'
            ],
            'name' => [
                'type' => Type::string(),
                'description' => 'The name of Crypto'
            ],
            'code' => [
                'type' => Type::string(),
                'description' => 'The code of Crypto'
            ],
            'symbol' => [
                'type' => Type::string(),
                'description' => 'The symbol of Crypto'
            ],
            'is_active' => [
                'type' => Type::string(),
                'description' => 'The is_active of Crypto'
            ],
            'preference' => [
                'type' => Type::string(),
                'description' => 'The preference of Crypto'
            ]
        ];
    }
}
