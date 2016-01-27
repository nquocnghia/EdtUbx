<?php

use QnNguyen\EdtUbxNS\EdtUbx;

class EdtUbxTest extends PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $edt = new EdtUbx('http://www.disvu.u-bordeaux1.fr/et/edt_etudiants2/Licence/Semestre2/g72873.xml');
        $this->assertTrue(strcmp($edt->getName(), 'Emploi du temps Groupe - IN601 GROUPE A1') === 0);
        $this->assertCount(251, $edt->getItems()); // 27 Jan 2016 18:54
    }

    public function testException()
    {
        $this->setExpectedException('Exception');
        new EdtUbx('http://www.disvu.u-bordeaux1.fr/et/edt_etudiants2/Licence/Semestre2/g72873.pdf');
    }

    public function testApplyFilter()
    {
        $edt1 = new EdtUbx('http://www.disvu.u-bordeaux1.fr/et/edt_etudiants2/Licence/Semestre2/g72873.xml');
        $edt2 = new EdtUbx('http://www.disvu.u-bordeaux1.fr/et/edt_etudiants2/Licence/Semestre2/g72873.xml');

        $whiteList = [
            'B1TR6W07' => '*' //anglais uniquement
        ];

        $blackList = [ // anglais uniquement
            'J1IN6011' => '*',
            'J1IN6012' => '*',
            'J1IN6013' => '*',
            'J1IN6014' => '*',
            'J1IN6016' => '*',
            'J1IN6017' => '*',
            'F1IN6017' => '*',
            'N1MA6W31' => '*',
            'J1INPW11' => '*',
            'J1INPM01' => '*'
        ];

        $edt1->apply_filter($whiteList, true);
        $this->assertCount(11, $edt1->getItems()); // 27 Jan 2016 18:54

        $edt2->apply_filter($blackList, false);
        $this->assertCount(11, $edt2->getItems()); // 27 Jan 2016 18:54
    }
}