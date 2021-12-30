<?php

namespace ASG\DMRAPI;

use ASG\DMRAPI\Exceptions\DmrApiException;

class Filter
{
    const EQUALS = 'eq';
    const NOT_EQUALS = 'neq';
    const LIKE = 'like';
    const CONTAINS = 'contains';
    const GREATER_THAN = 'gt';
    const LESS_THAN = 'lt';
    const GREATER_THAN_OR_EQUALS = 'gte';
    const LESS_THAN_OR_EQUALS = 'lte';
    const SIZE = 'size';
    const EXISTS = 'exists';

    const VALID_OPERATORS = [
        self::EQUALS,
        self::NOT_EQUALS,
        self::LIKE,
        self::CONTAINS,
        self::GREATER_THAN,
        self::LESS_THAN,
        self::GREATER_THAN_OR_EQUALS,
        self::LESS_THAN_OR_EQUALS,
        self::SIZE,
        self::EXISTS,
    ];

    /**
     * @var string
     */
    private $field;

    /**
     * @var string
     */
    private $operator;

    /**
     * @var string
     */
    private $value;

    /**
     * @param string $field
     * @param string $operator
     * @param string $value
     */
    protected function __construct($field, $operator, $value)
    {
        $this->field = $field;
        $this->operator = $operator;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @return string
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $field
     * @param string $operator
     * @param mixed $value
     * @return Filter
     * @throws DmrApiException
     */
    public static function make($field, $operator, $value)
    {
        if (!in_array($operator, self::VALID_OPERATORS)) {
            throw new DmrApiException('Invalid Operator.');
        }
        if ($value instanceof \DateTimeInterface) {
            $value = 'datetime(' . $value->format('YmdHis') . ')';
        }
        if (is_int($value)) {
            $value = 'int(' . $value . ')';
        }
        if (is_float($value)) {
            $value = 'float(' . $value . ')';
        }
        if (is_bool($value)) {
            $value = 'bool(' . ($value ? 'true' : 'false') . ')';
        }
        if ($value === null) {
            $value = 'null()';
        }
        if (!is_string($value)) {
            throw new DmrApiException('Failed to parse value.');
        }
        return new Filter($field, $operator, $value);
    }
}