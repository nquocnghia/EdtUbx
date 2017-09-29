<?php

use QnNguyen\EdtUbxNS\Condition\CF;
use QnNguyen\EdtUbxNS\Core\EdtUbxItem;

class ConditionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var EdtUbxItem
     */
    private $item;


    protected function setUp()
    {
        $now = new DateTime();
        $this->item = new EdtUbxItem('CODE1', $now, $now);
        $this->item->setCategory("CAT1");
        $this->item->setGroups(['GROUP1', 'GROUPE2']);
    }

    public function testMatchInString()
    {
        $cond1 = CF::_string('code', 'CODE1');
        $cond2 = CF::_string('code', 'CODE2');

        $this->assertTrue($cond1->evaluate($this->item));
        $this->assertFalse($cond2->evaluate($this->item));
    }

    public function testMatchInArray()
    {
        $cond1 = CF::_array('groups', 'GROUP1');
        $cond2 = CF::_array('groups', 'GROUP3');

        $this->assertTrue($cond1->evaluate($this->item));
        $this->assertFalse($cond2->evaluate($this->item));
    }

    public function testOr()
    {
        $cond1 = CF::_string('code', 'CODE1');
        $cond2 = CF::_string('code', 'CODE2');
        $cond3 = CF::_string('code', 'CODE3');
        $cond4 = CF::_string('category', 'CAT1');

        // true || true
        $this->assertTrue(CF::_or($cond1, $cond4)->evaluate($this->item));
        // true || false
        $this->assertTrue(CF::_or($cond1, $cond2)->evaluate($this->item));
        // false || true
        $this->assertTrue(CF::_or($cond2, $cond1)->evaluate($this->item));
        // false || false
        $this->assertFalse(CF::_or($cond2, $cond3)->evaluate($this->item));
        // false || true || false
        $this->assertTrue(CF::_or($cond2, $cond4, $cond3)->evaluate($this->item));
    }

    public function testAnd()
    {
        $cond1 = CF::_string('code', 'CODE1');
        $cond2 = CF::_string('code', 'CODE2');
        $cond3 = CF::_string('category', 'CAT1');
        $cond4 = CF::_string('category', 'CAT2');

        // true && true
        $this->assertTrue(CF::_and($cond1, $cond3)->evaluate($this->item));
        // true && false
        $this->assertFalse(CF::_and($cond1, $cond4)->evaluate($this->item));
        // false && true
        $this->assertFalse(CF::_and($cond2, $cond1)->evaluate($this->item));
        // false && false
        $this->assertFalse(CF::_and($cond2, $cond4)->evaluate($this->item));
        // false && false && true
        $this->assertFalse(CF::_and($cond2, $cond4, $cond1)->evaluate($this->item));
    }

    public function testNot()
    {
        $cond1 = CF::_string('code', 'CODE1');
        $cond2 = CF::_string('code', 'CODE2');

        // !true
        $this->assertFalse(CF::_not($cond1)->evaluate($this->item));
        // !false
        $this->assertTrue(CF::_not($cond2)->evaluate($this->item));
    }
}