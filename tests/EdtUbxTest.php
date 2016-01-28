<?php

use QnNguyen\EdtUbxNS\EdtIndex;
use QnNguyen\EdtUbxNS\EdtUbx;

class EdtUbxTest extends PHPUnit_Framework_TestCase
{
    private $urls;

    public function testConstructor()
    {
        $urls = $this->getUrls();
        $edt = new EdtUbx($urls['Licence']['Semestre2']['IN601']['GROUPE A1']);
        $this->assertTrue(strcmp($edt->getName(), 'Emploi du temps Groupe - IN601 GROUPE A1') === 0);
        $this->assertCount(249, $edt->getItems()); // 27 Jan 2016 18:54
    }

    public function getUrls()
    {
        if (!isset($this->urls))
            $this->urls = EdtIndex::fetch();

        return $this->urls;
    }

    public function testException()
    {
        $this->setExpectedException('Exception');
        new EdtUbx('http://www.disvu.u-bordeaux1.fr/et/edt_etudiants2/Licence/Semestre2/g72873.pdf');
    }

    public function testApplyFilter()
    {
        $urls = $this->getUrls();
        $edt1 = new EdtUbx($urls['Licence']['Semestre2']['IN601']['GROUPE A1']);
        $edt2 = new EdtUbx($urls['Licence']['Semestre2']['IN601']['GROUPE A1']);

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
        $this->assertCount(11, $edt1->getItems()); // 27 Jan 2016 18:54

        $edt2->apply_filter($blackList, false);
        $this->assertCount(11, $edt2->getItems()); // 27 Jan 2016 18:54
    }
}