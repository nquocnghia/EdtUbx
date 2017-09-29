<?php

namespace QnNguyen\EdtUbxNS\Condition;


use QnNguyen\EdtUbxNS\Core\EdtUbxItem;

/**
 * Class AndCondition
 * Represents the AND operator
 * @package QnNguyen\EdtUbxNS\Condition
 */
class AndCondition extends PolyadicCondition
{
    /**
     * Return true if all conditions are satisfied
     * @param EdtUbxItem $item
     * @return boolean
     */
    function evaluate(EdtUbxItem $item)
    {
        foreach ($this->conditions as $condition)
            if ($condition->evaluate($item) === false)
                return false;

        return true;
    }
}