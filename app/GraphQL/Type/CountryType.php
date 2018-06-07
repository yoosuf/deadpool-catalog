<?php

namespace App\GraphQL\Type;

use GraphQL\Type\Definition\Type;
use Folklore\GraphQL\Support\Type as BaseType;
use GraphQL;

class CountryType extends BaseType
{
    protected $attributes = [
        'name' => 'CountryType',
        'description' => 'A type'
    ];

    public function fields()
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'The id of the user'
            ],
            'name' => [
                'type' => Type::string(),
                'description' => 'The email of user'
            ],
            'nice_name' => [
                'type' => Type::string(),
                'description' => 'The email of user'
            ],
            'iso' => [
                'type' => Type::string(),
                'description' => 'The email of user'
            ],
            'iso3' => [
                'type' => Type::string(),
                'description' => 'The email of user'
            ],
            'phone_code' => [
                'type' => Type::string(),
                'description' => 'The phone_code of country'
            ],
            'preference' => [
                'type' => Type::string(),
                'description' => 'The preference of country table'
            ]
        ];
    }
}
