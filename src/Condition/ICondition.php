<?php

namespace QnNguyen\EdtUbxNS\Condition;

use QnNguyen\EdtUbxNS\Core\EdtUbxItem;

/**
 * Interface ICondition
 * Represents a test condition
 * @package QnNguyen\EdtUbxNS\Condition
 */
interface ICondition
{
    /**
     * Evaluate the given item against this condition
     * @param EdtUbxItem $item
     * @return boolean
     */
    function evaluate(EdtUbxItem $item);
}