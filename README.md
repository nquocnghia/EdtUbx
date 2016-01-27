# EdtUbx
University of Bordeaux's timetable parser

With this library, you can easily fetch new timetable from Ubx's server, filter it by excluding classes and export it to iCalendar (.ics) format.

### Basic usage

##### Fetch all timetable URLs

````
$urls = EdtIndex::fetch(); // return a 4-dimensional array
//1st dimension: Licence|Master1|Master2
//2nd dimension: Semestre1|Semestre2
//3rd dimension: ue code (eg. IN601)
//4th dimension: <Group name>|(nothing)

//usage example
$url = $urls['Licence']['Semestre2']['IN601']['GROUPE A1']
````

##### Fetch and export a timetable

````
//Init EdtUbx object
$edt = new EdtUbx($url);

//Export to ics format
$edt->export($with_header = true);

//Filtering: blacklist example
$filter = [
    'J1IN6011' => [
        'category' => ['in' => 'td( machine)?'], //regex accepted
        'notes' => ['notIn' => 'groupe( )?4']
    ]
];
$edt->apply_filter($filter); //will exclude all the classes 'TD' that is not for 'Groupe 4'

//Filtering: whitelist example
$filter = ['B1TR6W07' => '*'];
$edt->apply_filter($filter, true); // will exlude all the classes except ones that have code 'B1TR6W07'
````

### Dependencies
- [eluceo/ical](https://github.com/markuspoerschke/iCal)

### License
See [LICENSE](LICENSE)

_Tweet me [@nquocnghia](https://twitter.com/nquocnghia "nquocnghia on twitter")_