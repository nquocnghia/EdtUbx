<?php

namespace QnNguyen\EdtUbxNS\Condition;

use QnNguyen\EdtUbxNS\Core\EdtUbxItem;

/**
 * Class PropertyCondition
 * Check if the item has a property that satisfies the condition
 * @package QnNguyen\EdtUbxNS\Condition
 */
abstract class PropertyCondition implements ICondition
{
    protected $propertyName;
    protected $regexPattern;

    /**
     * MatchInString constructor.
     * @param $propertyName
     * @param $regexPattern
     */
    public function __construct($propertyName, $regexPattern)
    {
        $this->propertyName = $propertyName;
        $this->regexPattern = $regexPattern;
    }

    /**
     * @param EdtUbxItem $item
     * @return boolean
     */
    function evaluate(EdtUbxItem $item)
    {
        //if property exists
        if (property_exists($item, $this->propertyName)) {
            $v = $item->{"get" . ucfirst(strtolower($this->propertyName))}();
            if ($this->typeCheck($v) === true) {
                return $this->doTest($v);
            }
        }

        return false;
    }

    /**
     * Verify $value's type
     * @param $value
     * @return bool
     */
    protected abstract function typeCheck($value);

    /**
     * Check if $value satisfy our condition
     * @param $value
     * @return boolean
     */
    protected abstract function doTest($value);

    /**
     * Regex test
     * @param $pattern
     * @param $value
     * @return bool True if $pattern is matched in $string; False if is not matched or $pattern is invalid
     */
    protected function _regex_test($pattern, $value)
    {
        try {
            return preg_match('/' . $pattern . '/i', $value) === 1;
        } catch (\Exception $e) {
            //regex pattern is invalid
            return false;
        }
    }
}