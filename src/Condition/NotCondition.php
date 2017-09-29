<?php

namespace QnNguyen\EdtUbxNS\Condition;

use QnNguyen\EdtUbxNS\Core\EdtUbxItem;

/**
 * Class NotCondition
 * Represents the NOT operator
 * @package QnNguyen\EdtUbxNS\Condition
 */
class NotCondition implements ICondition
{
    /**
     * @var ICondition
     */
    private $condition;

    /**
     * NotCondition constructor.
     * @param ICondition $condition
     */
    public function __construct(ICondition $condition)
    {
        $this->condition = $condition;
    }

    /**
     * @param EdtUbxItem $item
     * @return boolean
     */
    function evaluate(EdtUbxItem $item)
    {
        return !$this->condition->evaluate($item);
    }
}