<?php

namespace App\GraphQL\Query;

use App\ExchangeData;
use Folklore\GraphQL\Support\Query;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use GraphQL;

class ExchangeDataQuery extends Query
{
    protected $attributes = [
        'name' => 'ExchangeDataQuery',
        'description' => 'A query'
    ];

    public function type()
    {
        return Type::listOf(GraphQL::type('ExchangeDataType'));
    }

    public function args()
    {
        return [
            'exchange_id' => ['name' => 'exchange_id', 'type' => Type::int()],
            'preference' => ['name' => 'preference', 'type' => Type::string()]
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $info)
    {
        if (isset($args['exchange_id'])) {
            return ExchangeData::where('exchange_id' , $args['exchange_id'])->get();
        } else {
            return ExchangeData::all();
        }
    }
}
