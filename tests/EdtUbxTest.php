<?php

use QnNguyen\EdtUbxNS\Condition\CF;
use QnNguyen\EdtUbxNS\Core\EdtIndex;
use QnNguyen\EdtUbxNS\Core\EdtUbx;

class EdtUbxTest extends PHPUnit_Framework_TestCase
{
    public function testFetchUrls()
    {
        $urls = EdtIndex::fetch(); //Should not throw an exception
        $this->assertArrayHasKey('Licence', $urls);
        $this->assertArrayHasKey('Semestre1', $urls['Licence']);
    }

    public function testConstructor()
    {
        $edt = EdtUbx::makeFromUrl('tests/IN601A1.xml');
        $this->assertTrue(strcmp($edt->getName(), 'Emploi du temps Groupe - IN601 GROUPE A1') === 0);
        $this->assertCount(253, $edt->getItems());
    }

    public function testException()
    {
        $this->setExpectedException('Exception');
        EdtUbx::makeFromUrl('http://www.disvu.u-bordeaux1.fr/et/edt_etudiants2/Licence/Semestre2/g72873.pdf');
    }

    public function testApplyFilter()
    {
        $edt = EdtUbx::makeFromUrl('tests/IN601A1.xml');

        // anglais uniquement
        $whiteList = CF::_string('code', 'B1TR6W07');

        $this->assertCount(11, $edt->filter($whiteList)->getItems());

        // anglais uniquement
        $blackList = CF::_not(
            CF::_or(
                CF::_string('code', 'J1IN6011'),
                CF::_string('code', 'J1IN6012'),
                CF::_string('code', 'J1IN6013'),
                CF::_string('code', 'J1IN6014'),
                CF::_string('code', 'J1IN6016'),
                CF::_string('code', 'J1IN6017'),
                CF::_string('code', 'F1IN6017'),
                CF::_string('code', 'N1MA6W31'),
                CF::_string('code', 'J1INPW11'),
                CF::_string('code', 'J1INPM01')
            ));

        $this->assertCount(11, $edt->filter($blackList)->getItems());
    }
}