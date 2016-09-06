<?php

namespace QnNguyen\EdtUbxNS;

use DateTime;

class EdtUbxItem
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var string
     */
    private $code;
    /**
     * @var string
     */
    private $category = '';

    /**
     * @var array
     */
    private $profs = [];
    /**
     * @var array
     */
    private $locations = [];
    /**
     * @var array
     */
    private $groups = [];

    /**
     * @var string
     */
    private $notes = '';

    /**
     * @var DateTime
     */
    private $dtStart;
    /**
     * @var DateTime
     */
    private $dtEnd;

    /**
     * EdtUbxItem constructor.
     * @param $code
     * @param DateTime $dtStart
     * @param DateTime $dtEnd
     */
    public function __construct($code, DateTime $dtStart, DateTime $dtEnd)
    {
        $this->setCode($code);
        $this->dtStart = $dtStart;
        $this->dtEnd = $dtEnd;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @throws \Exception
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param string $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * @return array
     */
    public function getProfs()
    {
        return $this->profs;
    }

    /**
     * @param array $profs
     */
    public function setProfs($profs)
    {
        $this->profs = $profs;
    }

    /**
     * @return array
     */
    public function getLocations()
    {
        return $this->locations;
    }

    /**
     * @param array $locations
     */
    public function setLocations($locations)
    {
        $this->locations = $locations;
    }

    /**
     * @return array
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * @param array $groups
     */
    public function setGroups($groups)
    {
        $this->groups = $groups;
    }

    /**
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * @param string $notes
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;
    }

    /**
     * @return DateTime
     */
    public function getDtStart()
    {
        return $this->dtStart;
    }

    /**
     * @param DateTime $dtStart
     */
    public function setDtStart(DateTime $dtStart)
    {
        $this->dtStart = $dtStart;
    }

    /**
     * @return DateTime
     */
    public function getDtEnd()
    {
        return $this->dtEnd;
    }

    /**
     * @param DateTime $dtEnd
     */
    public function setDtEnd(DateTime $dtEnd)
    {
        $this->dtEnd = $dtEnd;
    }
}