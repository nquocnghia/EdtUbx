<?php

namespace QnNguyen\EdtUbxNS\Condition;


use QnNguyen\EdtUbxNS\Core\EdtUbxItem;

/**
 * Class OrCondition
 * Represents the OR operator
 * @package QnNguyen\EdtUbxNS\Condition
 */
class OrCondition extends PolyadicCondition
{
    /**
     * Return true if at least 1 condition is satisfied
     * @param EdtUbxItem $item
     * @return boolean
     */
    function evaluate(EdtUbxItem $item)
    {
        foreach ($this->conditions as $condition)
            if ($condition->evaluate($item) === true)
                return true;

        return false;
    }
}