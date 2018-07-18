<?php

namespace App\GraphQL\Type;

use Carbon\Carbon;
use GraphQL\Language\AST\IntValueNode;
use Folklore\GraphQL\Support\Type as BaseType;
use GraphQL\Type\Definition\IntType;

class TimestampType extends BaseType
{
    /**
     * @var string
     */
    //public $name = "Timestamp";

    /**
     * @var string
     */
    //public $description = "A UNIX timestamp represented as an integer";

    protected $attributes = [
        'name' => 'ExchangeLogType',
        'description' => 'A type'
    ];

    /**
     * Serializes an internal value to include in a response.
     *
     * @param mixed $value
     * @return mixed
     */
    public function serialize($value)
    {
        return $this->toTimestamp($value);
    }

    /**
     * Parses an externally provided value (query variable) to use as an input
     *
     * @param mixed $value
     * @return mixed
     */
    public function parseValue($value)
    {
        return $this->toTimestamp($value);
    }

    /**
     * Parses an externally provided literal value (hardcoded in GraphQL query)
     * to use as an input
     *
     * @param \GraphQL\Language\AST\Node $valueNode
     * @return mixed
     */
    public function parseLiteral($ast)
    {
        if ($ast instanceof IntValueNode) {
            $val = (int) $ast->value;
            if ($ast->value === (string) $val && self::MIN_INT <= $val && $val <= self::MAX_INT) {
                return Carbon::createFromTimestamp($val);
            }
        }
        return null;
    }

    /**
     * Turn a value into a timestamp.
     *
     * @param mixed $value Could be anything the Carbon constructor can parse
     * (Carbon, DateTime, string, ...) or a timestamp integer
     *
     * @return int
     */
    protected function toTimestamp($value)
    {
        if (is_int($value)) {
            return $value;
        }
        return (new Carbon($value))->getTimestamp();
    }
}