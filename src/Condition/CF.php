<?php

namespace QnNguyen\EdtUbxNS\Condition;

/**
 * Class CF (ConditionFactory)
 * @package QnNguyen\EdtUbxNS\Condition
 */
class CF
{
    /**
     * @param ICondition[] ...$conditions
     * @return AndCondition
     */
    public static function _and(ICondition ...$conditions)
    {
        return new AndCondition(...$conditions);
    }

    /**
     * @param ICondition[] ...$conditions
     * @return OrCondition
     */
    public static function _or(ICondition ...$conditions)
    {
        return new OrCondition(...$conditions);
    }

    /**
     * @param ICondition $condition
     * @return NotCondition
     */
    public static function _not(ICondition $condition)
    {
        return new NotCondition($condition);
    }

    /**
     * @param $propertyName
     * @param $regexPattern
     * @return MatchInString
     */
    public static function _string($propertyName, $regexPattern)
    {
        return new MatchInString($propertyName, $regexPattern);
    }

    /**
     * @param $propertyName
     * @param $regexPattern
     * @return MatchInArray
     */
    public static function _array($propertyName, $regexPattern)
    {
        return new MatchInArray($propertyName, $regexPattern);
    }
}