<?php

namespace App\GraphQL\Query;

use Folklore\GraphQL\Support\Query;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use GraphQL;

class CountryCurrencyQuery extends Query
{
    protected $attributes = [
        'name' => 'CountryCurrencyQuery',
        'description' => 'A query'
    ];

    public function type()
    {
        return Type::listOf(GraphQL::type('CountryCurrencyType'));

    }

    public function args()
    {
        return [
            'country_id' => ['name' => 'country_id', 'type' => Type::int()],
            'currency_id' => ['name' => 'currency_id', 'type' => Type::int()],
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $info)
    {
        return [];
    }
}
