# EdtUbx
Cette librairie permet de récupérer l'emploi du temps de différentes filières de l'Université de Bordeaux, de le filtrer en excluant les cours et de l'exporter en format iCalendar (.ics).

[![Build Status](https://travis-ci.org/nquocnghia/EdtUbx.svg?branch=master)](https://travis-ci.org/nquocnghia/EdtUbx)

### Prérequis
L'extension `php-xml` est requis pour l'usage de cette librairie.

### Installation

Vous pouvez télécharger directement cette librairie depuis la page [Releases](https://github.com/nquocnghia/EdtUbx/releases) dans ce dépôt.

Vous pouvez aussi l'installer avec [Composer](http://getcomposer.org). Il suffit de lancer la commande suivante dans le dossier de votre projet:

```sh
$ composer require nquocnghia/edtubx
```

### Usage

##### Récupérer tous les URLS des emplois du temps

```php
// cela retourne un array de 4 dimensions
$urls = \QnNguyen\EdtUbxNS\Core\EdtIndex::fetch(); 

// 1st dimension: Licence|Master1|Master2
// 2nd dimension: Semestre1|Semestre2
// 3rd dimension: Série de cours (ie. IN601)
// 4th dimension: <Nom du groupe de TD>|(rien)

// exemple: l'url vers l'emploi du temps du 2è semestre du groupe A1 de la Licence Informatique (IN601)
$url = $urls['Licence']['Semestre2']['IN601']['GROUPE A1']
```

##### Télécharger, analyser et export un emploi du temps

```php
use \QnNguyen\EdtUbxNS\Core\EdtUbx;

// Initialiser l'objet EdtUbx
$edt = EdtUbx::makeFromUrl($url);

// ou bien si vous voulez remplacer le nom d'une/des UE(s)
$edt = EdtUbx::makeFromUrl($url, [
    'codeUE1' => 'nouveau nom1',
    'codeUE2' => 'nouveau nom2',
    ...
]);

// Générer un fichier iCalendar (.ics)
$edt->toICS();
```

##### Filtrage de l'emploi du temps
Parfois vous auriez des UEs en option ou qui ne vous concernent pas. Vous pouvez donc les exclure de votre emploi du temps pour le rendre plus lisible.

Cette librairie fournit une structure logique pour effectuer le filtrage:

```php
interface IContidion {
    /**
     * @return boolean
     */
    function evaluate(EdtUbxItem $item);
}

// les tests
abstract class PropertyCondition implements ICondition {}
class MatchInString extends PropertyCondition {}
class MatchInArray extends PropertyCondition {}

// les opérateurs logiques
abstract class PolyadicCondition implements ICondition {}
class AndCondition extends PolyadicCondition {}
class OrCondition extends PolyadicCondition {}
class NotCondition extends PolyadicCondition {}

// La classe CF (ConditionFactory) sert à générer les classes
// de la famille ICondition en raccoursissant le syntax.
class CF {
    public static function _string($propertyName, $regexPattern) {}
    public static function _array($propertyName, $regexPattern) {}
    public static function _and(ICondition ...$conditions) {}
    public static function _or(ICondition ...$conditions) {}
    public static function _not(ICondition $condition) {}
}
```

L'objet de type `EdtUbx` permet donc de filtrer l'emploi du temps avec la méthode `filter(ICondition $condition)`. Le résultat renvoyé sera la copie filtrée de l'emploi du temps courant.

###### Exemple de filtrage

Un étudiant en L3 veut exclure de son emploi du temps:
* l'emsemble des cours de l'UE `J1IN6012`
* tous les **TD** de l'UE `J1IN6011` qui ne sont pas pour le **groupe 4**

```php
// !(J1IN6012 || (J1IN6011 && td && groupe4))
$filteredEdt = $edt->filter(
    CF::_not(
        CF::_or(
            CF::_string('code', 'J1IN6012'),
            CF::_and(
                CF::_string('code', 'J1IN6011'),
                CF::_string('category', 'td( machine)?'),
                CF::_string('notes', 'groupe( )?4')
                // bien lu, parfois le groupe est indiqué dans 'notes'
                // et non pas dans 'groupes' T_T
            )
        )
    )
);
```

### Dépendances
- [eluceo/ical](https://github.com/markuspoerschke/iCal)

### License
See [LICENSE](LICENSE)

_Tweet me [@nquocnghia](https://twitter.com/nquocnghia "nquocnghia on twitter")_
