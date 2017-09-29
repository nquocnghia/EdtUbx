<?php

namespace QnNguyen\EdtUbxNS\Condition;

/**
 * Class MatchInArray
 * Check if an element exists in an array
 * @package QnNguyen\EdtUbxNS\Condition
 */
class MatchInArray extends PropertyCondition
{
    /**
     * @param $value
     * @return bool
     */
    function typeCheck($value)
    {
        return is_array($value);
    }

    /**
     * @param $value
     * @return boolean
     */
    function doTest($value)
    {
        foreach ($value as $arrayItem) {
            if ($this->_regex_test($this->regexPattern, $arrayItem)) {
                return true;
            }
        }

        return false;
    }
}