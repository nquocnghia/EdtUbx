<?php

namespace QnNguyen\EdtUbxNS\Core;

use Eluceo\iCal\Component\Calendar;
use Eluceo\iCal\Component\Event;
use QnNguyen\EdtUbxNS\Condition\ICondition;

class EdtUbx
{
    static $timezone = 'Europe/Paris';

    /**
     * @var string
     */
    private $name;
    /**
     * @var EdtUbxItem[]
     */
    private $items = [];

    /**
     * EdtUbx constructor.
     * @param string $url
     * @param array $name_map
     * @throws \Exception
     * @return EdtUbx
     */
    public static function makeFromUrl($url, $name_map = [])
    {
        //download timetable from url
        $xml = file_get_contents($url);

        if ($xml === false) //download failed
            throw new \Exception('Download failed: ' . $url);

        //parse downloaded file
        $parser = simplexml_load_string($xml);
        if ($parser === false)
            throw new \Exception('Parse failed');

        if (!isset($parser->option) || !isset($parser->option->subheading) || !isset($parser->event))
            throw new \Exception('Invalid timetable file');

        /** @var $edt EdtUbx */
        $edt = new EdtUbx();

        //set timetable name
        $edt->setName((string)$parser->option->subheading);

        //main loop
        foreach ($parser->event as $ev) {
            //calculate event duration
            $interval = new \DateInterval('P' . (string)$ev->day . 'D');

            $dtStart = \DateTime::createFromFormat('d/m/Y G:i',
                sprintf('%s %s', (string)$ev['date'], (string)$ev->starttime)
                , new \DateTimeZone(self::$timezone));
            $dtStart->add($interval);

            $dtEnd = \DateTime::createFromFormat('d/m/Y G:i',
                sprintf('%s %s', (string)$ev['date'], (string)$ev->endtime)
                , new \DateTimeZone(self::$timezone));
            $dtEnd->add($interval);

            //extract event code
            $full_name = (string)$ev->resources->module->item;
            $code = preg_match('/^[A-Z0-9]{8}/', $full_name) === 0 ? -1 : substr($full_name, 0, 8);
            $name = isset($name_map[strval($code)]) ? $name_map[$code] : $full_name;

            //init event attributes
            $item = new EdtUbxItem($code, $dtStart, $dtEnd);
            $item->setName($name);
            $item->setCategory((string)$ev->category);
            $item->setProfs((array)$ev->resources->staff->item);
            $item->setLocations((array)$ev->resources->room->item);
            $item->setGroups((array)$ev->resources->group->item);
            $item->setNotes(isset($ev->notes) ? (string)$ev->notes : '');

            //add event to events collection
            $edt->addItem($item);
        }

        return $edt;
    }

    /**
     * Add an item to the array
     * @param EdtUbxItem $item
     */
    public function addItem(EdtUbxItem $item)
    {
        $this->items[] = $item;
    }

    /**
     * Create iCalendar (.ics) file
     * @return string
     */
    public function toICS()
    {
        $vCalendar = new Calendar('edt.u-bordeaux');
        $vCalendar
            ->setTimezone(self::$timezone)
            ->setName($this->name)
            ->setDescription($this->name);

        /** @var EdtUbxItem $item */
        foreach ($this->items as $item) {
            $profs = implode(', ', $item->getProfs());
            $location = implode(', ', $item->getLocations());
            $groups = implode(', ', $item->getGroups());

            $vEvent = new Event();
            $vEvent
                ->setUseTimezone(true)
                ->setDtStart($item->getDtStart())
                ->setDtEnd($item->getDtEnd())
                ->setCategories($item->getCategory())
                ->setLocation($location);

            $vEvent->setDescription(sprintf("%s\n%s%s%s%s%s",
                $item->getName(),
                $item->getCategory(),
                $location !== '' ? "\n$location" : '',
                $profs !== '' ? "\n$profs" : '',
                $groups !== '' ? "\n$groups" : '',
                $item->getNotes() ? "\nNotes: " . $item->getNotes() : ''
            ));

            $vEvent->setSummary(sprintf('%s (%s)%s',
                $item->getName(),
                $item->getCategory(),
                $profs !== '' ? " - $profs" : ''
            ));

            $vCalendar->addComponent($vEvent);
        }

        return $vCalendar->render();
    }

    /**
     * Create a filtered copy of this Edt
     * @param ICondition $condition
     * @return EdtUbx
     */
    public function filter(ICondition $condition)
    {
        $filteredEdt = $this->copy();

        /* @var EdtUbxItem $item */
        foreach ($filteredEdt->getItems() as $k => $item) {
            if ($condition->evaluate($item) === false) {
                $filteredEdt->removeItem($k, false);
            }
        }

        $filteredEdt->setItems(array_values($filteredEdt->getItems()));

        return $filteredEdt;
    }

    /**
     * Clone this Edt
     * @return EdtUbx
     */
    public function copy()
    {
        /** @var $edt EdtUbx* */
        $edt = new EdtUbx();
        $edt->setName($this->name);
        $edt->setItems($this->getItems());

        return $edt;
    }

    /**
     * Get items array
     * @return EdtUbxItem[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Set items array
     * @param EdtUbxItem[] $items
     */
    public function setItems($items)
    {
        $this->items = $items;
    }

    /**
     * Remove an item
     * @param int $index
     * @param bool $reindex Set it true to reindex the items array
     */
    public function removeItem($index, $reindex = true)
    {
        unset($this->items[$index]);
        if ($reindex === true) {
            $this->items = array_values($this->items);
        }
    }

    /**
     * Get an item
     * @param int $index
     * @return EdtUbxItem
     */
    public function getItem($index)
    {
        return $this->items[$index];
    }

    /**
     * Get Edt name
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set Edt name
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
}
