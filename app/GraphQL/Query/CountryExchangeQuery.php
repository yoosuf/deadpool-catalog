<?php

namespace App\GraphQL\Query;

use App\Country;
use App\Exchange;
use Folklore\GraphQL\Support\Query;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use GraphQL;

class CountryExchangeQuery extends Query
{
    protected $attributes = [
        'name' => 'CountryExchangeQuery',
        'description' => 'A query'
    ];

    public function type()
    {
        return Type::listOf(GraphQL::type('CountryExchangeType'));

    }

    public function args()
    {
        return [
            'country_id' => ['name' => 'country_id', 'type' => Type::int()],
            'exchange_id' => ['name' => 'exchange_id', 'type' => Type::int()],
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $info)
    {
        return [];
    }
}
