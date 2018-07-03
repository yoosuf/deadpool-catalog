<?php

namespace App\GraphQL\Type;

use GraphQL\Type\Definition\Type;
use Folklore\GraphQL\Support\Type as BaseType;
use GraphQL;

class ExchangeLogType extends BaseType
{
    protected $attributes = [
        'name' => 'ExchangeLogType',
        'description' => 'A type'
    ];

    public function fields()
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::int()),
                'description' => 'The id of the Currency'
            ],
            'exchange_id' => [
                'type' => Type::int(),
                'description' => 'The name of Currency'
            ],
            'preference' => [
                'type' => Type::string(),
                'description' => 'The preference of Currency'
            ]
            // 'created_at' => [
            //     'type' => Type::timestamp(),
            //     'description' => 'The created time of Currency'
            // ]
        ];
    }
}
