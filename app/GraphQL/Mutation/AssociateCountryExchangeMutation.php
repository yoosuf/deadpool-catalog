<?php

namespace App\GraphQL\Mutation;

use App\Country;
use Folklore\GraphQL\Support\Mutation;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use GraphQL;

class AssociateCountryExchangeMutation extends Mutation
{
    protected $attributes = [
        'name' => 'AssociateCountryExchangeMutation',
        'description' => 'A mutation'
    ];

    public function type()
    {
        return Type::listOf(GraphQL::type('CountryExchangeType'));
    }

    public function args()
    {
        return [
            'country_id' => ['name' => 'country_id', 'type' => Type::int()],
            'exchange_id' => ['name' => 'exchange_id', 'type' => Type::int()]
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $info)
    {
        $country = Country::find($args['country_id']);

    
         $country->exchanges()->sync($args['exchange_id']);
         return $country;
    }
}
