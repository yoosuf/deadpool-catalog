<?php

namespace App\GraphQL\Query;

use App\Crypto;
use Folklore\GraphQL\Support\Query;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use GraphQL;

class CryptoQuery extends Query
{
    protected $attributes = [
        'name' => 'CryptoQuery',
        'description' => 'A query'
    ];

    public function type()
    {
        return Type::listOf(GraphQL::type('CryptoType'));
    }

    public function args()
    {
        return [
            'id' => ['name' => 'id', 'type' => Type::string()],
            'name' => ['name' => 'name', 'type' => Type::string()],
            'code' => ['name' => 'code', 'type' => Type::string()]
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $info)
    {
        if (isset($args['id'])) {
            return Crypto::where('id' , $args['id'])->get();
        } else if(isset($args['name'])) {
            return Crypto::where('name', $args['name'])->get();
        } else if(isset($args['code'])) {
            return Crypto::where('code', $args['code'])->get();
        }else {
            return Crypto::all();
        }
    }
}
