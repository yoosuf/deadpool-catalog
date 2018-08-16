<?php

namespace App\GraphQL\Mutation\Exchange;

use App\Exchange;
use Folklore\GraphQL\Support\Mutation;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use GraphQL;

class UpdateExchangesMutation extends Mutation
{
    protected $attributes = [
        'name' => 'UpdateExchangesMutation',
        'description' => 'A mutation'
    ];

    public function type()
    {
        return Type::listOf(GraphQL::type('ExchangeType'));
    }

    public function rules()
    {
        return [
            'name' => 'required|unique:exchanges'
        ];
    }

    public function args()
    {
        return [
            'id' => ['name' => 'id', 'type' => Type::int()],
            'name' => ['name' => 'name', 'type' => Type::string()],
            'description' => ['name' => 'description', 'type' => Type::string()],
            'is_active' => ['name' => 'is_active', 'type' => Type::boolean()]
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $info)
    {
        $exchanges = Exchange::find($args['id']);
        if(!$exchanges)
        {
            return null;
        }

        $fields = $args;
        $exchanges->update($fields);
        return $exchanges;

    }
}
