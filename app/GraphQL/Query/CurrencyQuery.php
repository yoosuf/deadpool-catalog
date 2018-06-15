<?php

namespace App\GraphQL\Query;

use App\Currency;
use Folklore\GraphQL\Support\Query;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use GraphQL;

class CurrencyQuery extends Query
{
    protected $attributes = [
        'name' => 'CurrencyQuery',
        'description' => 'A query'
    ];

    public function type()
    {
        return Type::listOf(GraphQL::type('CurrencyType'));
    }

    public function args()
    {
        return [
            'id' => ['name' => 'id', 'type' => Type::int()],
            'name' => ['name' => 'name', 'type' => Type::string()],
            'iso3' => ['name' => 'iso3', 'type' => Type::string()]
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $info)
    {
        if (isset($args['id'])) {
            return Currency::where('id' , $args['id'])->get();
        } else if(isset($args['name'])) {
            return Currency::where('name', $args['name'])->get();
        } else if(isset($args['iso3'])) {
            return Currency::where('iso3', $args['iso3'])->get();
        }else {
            return Currency::all();
        }
    }
}
