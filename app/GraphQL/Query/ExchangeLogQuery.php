<?php

namespace App\GraphQL\Query;

use App\ExchangeLog;
use Folklore\GraphQL\Support\Query;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use GraphQL;

class ExchangeLogQuery extends Query
{
    protected $attributes = [
        'name' => 'exchangeLogQuery',
        'description' => 'A query'
    ];

    public function type()
    {
        return Type::listOf(GraphQL::type('ExchangeLogType'));
    }

    public function args()
    {
        return [
            'exchange_id' => ['name' => 'exchange_id', 'type' => Type::int()],
            'preference' => ['name' => 'preference', 'type' => Type::string()],
            // 'created_at' => ['name' => 'created_at', 'type' => Type::string()]
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $info)
    {
        if (isset($args['exchange_id'])) {
            return ExchangeLog::where('exchange_id' , $args['exchange_id'])->get();
        } else {
            return ExchangeLog::all();
        }
    }
}
