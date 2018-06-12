<?php

namespace App\GraphQL\Mutation;

use App\Country;
use App\Currency;
use Folklore\GraphQL\Support\Mutation;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use GraphQL;

class AssociateCountryCurrencyMutation extends Mutation
{
    protected $attributes = [
        'name' => 'AssociateCountryCurrencyMutation',
        'description' => 'A mutation'
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
        $country = Country::find($args['country_id']);

        $country->currencies()->sync($args['currency_id']);

        return $country;
    }
}
