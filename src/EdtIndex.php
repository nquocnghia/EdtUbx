<?php

namespace QnNguyen\EdtUbxNS;


class EdtIndex
{
    private static $FILIERES = [
        'Licence' => 'Licence',
        'Master1' => 'Master/Master1',
        'Master2' => 'Master/Master2'
    ];
    private static $SEMESTRES = ['Semestre1', 'Semestre2'];
    private static $URL = 'https://edt-st.u-bordeaux.fr/etudiants/'; //finder.xml

    /**
     * After fetching the remote source, this should become a 4-dimensional array
     * 1st dimension: Licence|Master1|Master2
     * 2nd dimension: Semestre1|Semestre2
     * 3rd dimension: Course's code (eg. IN601)
     * 4th dimension: <Group name>|(nothing)
     * Example: $urls['Licence']['Semestre2']['IN601']['GROUPE A1']
     * @var array $urls
     */
    private static $urls = null;

    /**
     * Index constructor (Hidden)
     */
    private function __construct()
    {
        //We don't need this because we're implementing a freaking singleton pattern ^^
    }

    /**
     * Fetch latest timetables index from Ubx's server
     * @return array
     * @throws \Exception
     */
    public static function fetch()
    {
        if (is_null(self::$urls)) {
            self::$urls = [];

            foreach (self::$FILIERES as $f => $f_seg) {
                foreach (self::$SEMESTRES as $sem) {
                    $urlPrefix = self::$URL . $f_seg . '/' . $sem;
                    $url = $urlPrefix . '/finder.xml';

                    //download timetable from url
                    $xml = @file_get_contents($url);

                    if ($xml === false) { //download failed
                        error_log('Download failed: ' . $url);
                        continue;
                    }

                    //parse downloaded file
                    $parser = simplexml_load_string($xml);
                    if ($parser === false)
                        throw new \Exception('Parse failed');

                    foreach ($parser->resource as $res) {
                        //we're only looking for group
                        if ($res['type'] != 'group')
                            continue;

                        $file = $res->link[0]['href'];
                        $delim = $f == 'Licence' ? ' ' : ', ';
                        $ar = explode($delim, $res->name, 2);

                        if (!is_array($ar) || count($ar) === 0)
                            throw new \Exception('Invalid string');

                        $key = count($ar) === 2 ? trim($ar[1]) : '(nothing)';
                        self::$urls[$f][$sem][$ar[0]][$key] = $urlPrefix . '/' . $file;
                    }
                }
            }
        }

        return self::$urls;
    }
}
