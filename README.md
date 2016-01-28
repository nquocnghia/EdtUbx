# EdtUbx
University of Bordeaux's timetable parser

With this library, you can easily fetch new timetable from Ubx's server, filter it by excluding classes and export it to iCalendar (.ics) format.

### Installation

You can install this package by using [Composer](http://getcomposer.org), running this command:

```sh
composer require nquocnghia/edtubx
```

Link to Packagist: https://packagist.org/packages/nquocnghia/edtubx

### Basic usage

##### Fetch all timetable URLs

```php
$urls = \QnNguyen\EdtUbxNS\EdtIndex::fetch(); // return a 4-dimensional array
//1st dimension: Licence|Master1|Master2
//2nd dimension: Semestre1|Semestre2
//3rd dimension: Course's code (eg. IN601)
//4th dimension: <Group name>|(nothing)

//usage example
$url = $urls['Licence']['Semestre2']['IN601']['GROUPE A1']
```

##### Fetch and export a timetable

```php
//Init EdtUbx object
$edt = new \QnNguyen\EdtUbxNS\EdtUbx($url);

//Export to ics format
$edt->export($with_header = true);

//Filtering: blacklist example
$filter = [
    'J1IN6011' => [
        'category' => ['in' => 'td( machine)?'], // regex accepted
        'notes' => ['notIn' => 'groupe( )?4']
    ],
    'J1IN6012' => [] // empty array means uncontional matching
];
$edt->apply_filter($filter); // will exclude all the classes J1IN6012; and J1IN6011 of category 'TD' that is not for 'Groupe 4'

//Filtering: whitelist example
$filter = ['B1TR6W07' => []];
$edt->apply_filter($filter, true); // will exlude all the classes except ones that have code 'B1TR6W07'
```

### Dependencies
- [eluceo/ical](https://github.com/markuspoerschke/iCal)

### License
See [LICENSE](LICENSE)

_Tweet me [@nquocnghia](https://twitter.com/nquocnghia "nquocnghia on twitter")_