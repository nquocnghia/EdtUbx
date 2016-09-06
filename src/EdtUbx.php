<?php

namespace QnNguyen\EdtUbxNS;

use Eluceo\iCal\Component\Calendar;
use Eluceo\iCal\Component\Event;

class EdtUbx
{
    static $timezone = 'Europe/Paris';

    private $name;
    private $items = [];

    /**
     * EdtUbx constructor.
     * @param $url string
     * @param array $name_map
     * @throws \Exception
     */
    public function __construct($url, $name_map = [])
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

        //set timetable name
        $this->name = (string)$parser->option->subheading;

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
            $this->items[] = $item;
        }
    }

    /**
     * Create iCalendar (.ics) file
     * @param bool $with_header
     * @return string
     */
    public function toICS($with_header = true)
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

        if ($with_header === true) {
            header('Content-type: text/calendar; charset=utf-8');
            header('Content-Disposition: attachment; filename=calendar.ics');
        }

        return $vCalendar->render();
    }

    /**
     * Filter the current list of items.
     * If it is a whitelist filter, all the items on the list
     * will be excluded except those matched by the rules.
     *
     * Sample of $rules:
     * $rules = [
     *     'J1IN6016' => [
     *         'category' => ['in' => 'TD'],
     *         'notes' => ['notIn' => 'Groupe [1-4]'] //regex accepted
     *     ],
     *     'J1IN6011' => []
     * ];
     *
     * @param array $rules
     * @param bool $is_whitelist
     */
    public function apply_filter($rules, $is_whitelist = false)
    {
        /* @var EdtUbxItem $item */
        foreach ($this->items as $k => $item) {
            if (!isset($rules[$item->getCode()])) {
                if ($is_whitelist)
                    unset($this->items[$k]);
                continue;
            }

            $is_matched = $this->_is_matched($item, $rules[$item->getCode()]);
            if ($is_matched != $is_whitelist)
                unset($this->items[$k]);
        }
    }

    /**
     * Check if $item is matched by ALL the given $criteria.
     * Empty $criteria array means the $item is matched unconditionally
     *
     * Sample of $criteria:
     *
     * $criteria = [
     *     'category' => ['in' => 'TD|Cours'], //regex accepted
     *     'notes' => ['notIn' => 'Groupe 4']
     * ];
     *
     * $criteria = [];
     *
     * @param EdtUbxItem $item
     * @param array $criteria
     * @return bool
     * @throws \Exception
     */
    private function _is_matched(EdtUbxItem $item, $criteria)
    {
        if (!is_array($criteria))
            throw new \Exception('Invalid data type: array expected');

        //check if the given rules can be satisfied
        foreach ($criteria as $attr_name => $pat) {

            //if property exists
            if (property_exists($item, $attr_name)) {
                $v = $item->{"get" . ucfirst(strtolower($attr_name))}();

                //if there is a 'in' or 'notIn' rule to check..
                if (isset($pat['in']) && !empty($pat['in'])) {
                    if (is_string($v)) {

                        //It's a 'in' rule but the pattern doesn't match the subject
                        if ($this->_regex_test($pat['in'], $v) === false)
                            return false;

                    } else if (is_array($v)) {
                        $found = false;

                        foreach ($v as $array_item) {
                            if ($this->_regex_test($pat['in'], $array_item) === true) {
                                $found = true;
                                break;
                            }
                        }

                        if (!$found)
                            return false;
                    }
                } else if (isset($pat['notIn']) && !empty($pat['notIn'])) {
                    if (is_string($v)) {

                        //It's a 'notIn' rule but the pattern does match the subject
                        if ($this->_regex_test($pat['notIn'], $v) === true)
                            return false;

                    } else if (is_array($v)) {
                        $found = false;

                        foreach ($v as $array_item) {
                            if ($this->_regex_test($pat['notIn'], $array_item) === true) {
                                $found = true;
                                break;
                            }
                        }

                        if ($found)
                            return false;
                    }
                }
            }
        }

        //All good
        return true;
    }

    private function _regex_test($pattern, $value)
    {
        try {
            return preg_match('/' . $pattern . '/i', $value) === 1;
        } catch (\Exception $e) {
            //regex pattern is invalid
            return false;
        }
    }

    /**
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
