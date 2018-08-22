<?php

namespace App\GraphQL\Mutation\Exchange;

use App\Exchange;
use Folklore\GraphQL\Support\Mutation;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use GraphQL;

class CreateExchangesMutation extends Mutation
{
    protected $attributes = [
        'name' => 'createExchange',
        'description' => 'A mutation'
    ];

    public function type()
    {
        return Type::listOf(GraphQL::type('ExchangeType'));
    }

    public function rules()
    {
        return [
            'name' => 'required|unique:exchanges',
            'description' => 'required',
        ];
    }

    public function args()
    {
        return [
            'name' => ['name' => 'name', 'type' => Type::string()],
            'description' => ['name' => 'description', 'type' => Type::string()],
            'preference' => ['name' => 'preference', 'type' => Type::string()],
            'is_active' => ['name' => 'is_active', 'type' => Type::string()],
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $info)
    {
        $data = [
            'name' => $args['name'],
            'description' => $args['description'],
            'preference' => $args['preference'],
            'is_active' => $args['preference']
            
        ];
        $newData = Exchange::create($data);
        return $newData;
    }
}
