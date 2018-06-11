<?php

namespace App\GraphQL\Query;

use App\Exchange;
use Folklore\GraphQL\Support\Query;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use GraphQL;

class ExchangeQuery extends Query
{
    protected $attributes = [
        'name' => 'exchangeQuery',
        'description' => 'A query'
    ];

    public function type()
    {
        return Type::listOf(GraphQL::type('ExchangeType'));
    }

    public function args()
    {
        return [
            'id' => ['name' => 'id', 'type' => Type::string()],
            'name' => ['name' => 'name', 'type' => Type::string()],
            'description' => ['name' => 'description', 'type' => Type::string()],
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $info)
    {
        if (isset($args['id'])) {
            return Exchange::where('id' , $args['id'])->get();
        } else if(isset($args['name'])) {
            return Exchange::where('name', $args['name'])->get();
        } else {
            return Exchange::firstOrFail();
        }
    }
}
