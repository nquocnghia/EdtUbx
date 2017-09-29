<?php

namespace QnNguyen\EdtUbxNS\Condition;

/**
 * Class MatchInString
 * Check if a substring is found in a string
 * @package QnNguyen\EdtUbxNS\Condition
 */
class MatchInString extends PropertyCondition
{
    /**
     * @param $value
     * @return bool
     */
    function typeCheck($value)
    {
        return is_string($value);
    }

    /**
     * @param $value
     * @return boolean
     */
    function doTest($value)
    {
        return $this->_regex_test($this->regexPattern, $value);
    }
}