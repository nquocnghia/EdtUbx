<?php

use QnNguyen\EdtUbxNS\EdtIndex;
use QnNguyen\EdtUbxNS\EdtUbx;

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
        $edt = new EdtUbx('tests/IN601A1.xml');
        $this->assertTrue(strcmp($edt->getName(), 'Emploi du temps Groupe - IN601 GROUPE A1') === 0);
        $this->assertCount(253, $edt->getItems());
    }

    public function testException()
    {
        $this->setExpectedException('Exception');
        new EdtUbx('http://www.disvu.u-bordeaux1.fr/et/edt_etudiants2/Licence/Semestre2/g72873.pdf');
    }

    public function testApplyFilter()
    {
        $edt1 = new EdtUbx('tests/IN601A1.xml');
        $edt2 = new EdtUbx('tests/IN601A1.xml');

        $whiteList = [
            'B1TR6W07' => [] //anglais uniquement
        ];

        $blackList = [ // anglais uniquement
            'J1IN6011' => [],
            'J1IN6012' => [],
            'J1IN6013' => [],
            'J1IN6014' => [],
            'J1IN6016' => [],
            'J1IN6017' => [],
            'F1IN6017' => [],
            'N1MA6W31' => [],
            'J1INPW11' => [],
            'J1INPM01' => []
        ];

        $edt1->apply_filter($whiteList, true);
        $this->assertCount(11, $edt1->getItems());

        $edt2->apply_filter($blackList, false);
        $this->assertCount(11, $edt2->getItems());
    }
}