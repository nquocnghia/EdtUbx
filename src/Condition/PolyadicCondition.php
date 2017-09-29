<?php

namespace QnNguyen\EdtUbxNS\Condition;

/**
 * Class PolyadicCondition
 * Represents the polyadic operator
 * @package QnNguyen\EdtUbxNS\Condition
 */
abstract class PolyadicCondition implements ICondition
{
    /**
     * @var ICondition[]
     */
    protected $conditions;

    /**
     * AndCondition constructor.
     * @param ICondition[] $conditions
     */
    public function __construct(ICondition ...$conditions)
    {
        $this->conditions = $conditions;
    }
}