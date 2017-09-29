<?php

use QnNguyen\EdtUbxNS\Condition\CF;
use QnNguyen\EdtUbxNS\Core\EdtUbx;
use QnNguyen\EdtUbxNS\Core\EdtUbxItem;

class FilterTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var EdtUbx
     */
    private $edt;

    public function testMatchInString()
    {
        $filteredEdt = $this->edt->filter(CF::_string('code', 'CODE1'));
        $this->assertCount(1, $filteredEdt->getItems());
        $this->assertEquals('CODE1', $filteredEdt->getItem(0)->getCode());
    }

    public function testMatchInArray()
    {
        $filteredEdt = $this->edt->filter(CF::_array('groups', 'GROUP1'));
        $this->assertCount(2, $filteredEdt->getItems());
        $this->assertEquals('CODE1', $filteredEdt->getItem(0)->getCode());
        $this->assertEquals('CODE2', $filteredEdt->getItem(1)->getCode());
    }

    public function testOr()
    {
        $filteredEdt = $this->edt->filter(
            CF::_or(
                CF::_string('code', 'CODE1'),
                CF::_string('category', 'CAT1')
            )
        );
        $this->assertCount(2, $filteredEdt->getItems());
        $this->assertEquals('CODE1', $filteredEdt->getItem(0)->getCode());
        $this->assertEquals('CODE2', $filteredEdt->getItem(1)->getCode());
    }

    public function testAnd()
    {
        $filteredEdt = $this->edt->filter(
            CF::_and(
                CF::_string('code', 'CODE1'),
                CF::_string('category', 'CAT2')
            )
        );
        $this->assertCount(0, $filteredEdt->getItems());
    }

    public function testNot()
    {
        $filteredEdt = $this->edt->filter(
            CF::_not(
                CF::_string('category', 'CAT1')
            )
        );
        $this->assertCount(1, $filteredEdt->getItems());
        $this->assertEquals('CODE3', $filteredEdt->getItem(0)->getCode());
    }

    protected function setUp()
    {
        $this->edt = new EdtUbx();
        $this->edt->setName('Test Edt');

        $dt = new DateTime();

        $item1 = new EdtUbxItem('CODE1', $dt, $dt);
        $item1->setCategory('CAT1');
        $item1->setGroups(['GROUP1', 'GROUP2']);
        $this->edt->addItem($item1);

        $item2 = new EdtUbxItem('CODE2', $dt, $dt);
        $item2->setCategory('CAT1');
        $item2->setGroups(['GROUP1', 'GROUP3']);
        $this->edt->addItem($item2);

        $item3 = new EdtUbxItem('CODE3', $dt, $dt);
        $item3->setCategory('CAT2');
        $item3->setGroups(['GROUP2', 'GROUP3']);
        $this->edt->addItem($item3);

    }
}