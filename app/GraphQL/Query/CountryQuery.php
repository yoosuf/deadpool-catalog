<?php

namespace App\GraphQL\Query;

use App\Country;
use Folklore\GraphQL\Support\Query;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use GraphQL;

class CountryQuery extends Query
{
    protected $attributes = [
        'name' => 'CountryType',
        'description' => 'A query'
    ];

    public function type()
    {
        return Type::listOf(GraphQL::type('CountryType'));

    }

    public function args()
    {
        return [
            'id' => ['name' => 'id', 'type' => Type::int()],
            'nice_name' => ['name' => 'nice_name', 'type' => Type::string()],
            'iso3' => ['name' => 'iso3', 'type' => Type::string()]
        ];
    }

    public function resolve($root, $args, $context, ResolveInfo $info)
    {
        if (isset($args['id'])) {
            return Country::where('id' , $args['id'])->get();
        } else if(isset($args['nice_name'])) {
            return Country::where('nice_name', $args['nice_name'])->get();
        } else if(isset($args['iso3'])) {
            return Country::where('iso3', $args['iso3'])->get();
        }else {
            return Country::all();
        }
    }
}
