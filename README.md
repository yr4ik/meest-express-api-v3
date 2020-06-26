
## Meest Express API v3



### Composer
composer require yr4ik/meest-express-api-v3


### MeestExpress methods
* __construct($login, $pasword, $throwErrors = true, $connectionType = 'curl');
* setLogin(string $login);
* getLogin();
* setPassword(string $password);
* getPassword();
* setCookieStorageVar(string $var);
* getCookieStorageVar();
* setConnectionType(string curl | file_get_contents);
* getConnectionType();
* setFormat(string json | array | xml);
* getFormat();
* setToken(string $token, int $expire=0, string $refresh='')
* getToken();
* authorize();
* request(string $method, array $data, string $format=null)


### Search methods

* country(Filter); Filter prams: 
    * id OR countryIDOR
	* name OR countryDescr
	
* region(Filter); Filter prams: 
    *  id OR regionIDOR
    *  name OR regionDescrOR
    *  katuu OR regionKATUUOR
    *  country_id OR countryIDOR
    *  country OR countryDescr
	
* district(Filter); Filter prams: 
    *  id OR districtIDOR
    *  name OR districtDescrOR
    *  katuu OR districtKATUUOR
    *  region_id OR regionIDOR
    *  country OR regionDescr
	
* city(Filter); Filter prams: 
    *  id OR cityIDOR
    *  name OR cityDescrOR
    *  country_id OR countryIDOR
    *  district_id OR districtIDOR
    *  district OR districtDescrOR
    *  region_id OR regionIDOR
    *  region OR regionDescrOR
    *  region_katuu OR regionKATUU
	
* cityByZip(int $zip_code);

* address(Filter); Filter prams: 
    *  city_id OR cityIDOR
    *  address OR addressDescr
	
* branchTypes();

* branch(Filter); Filter prams: 
    *  num OR branchNoOR
    *  type_id OR branchTypeIDOR
    *  name OR branchDescrOR
    *  city_id OR cityIDOR
    *  city OR cityDescrOR
    *  district_id OR districtIDOR
    *  district OR districtDescrOR
    *  region_id OR regionIDOR
    *  region OR regionDescr
	
* payTerminal(float $latitude, float $longitude);


### Search result methods
* getResponse();
* getResponseSource();
* getStatus();
* getInfo();
* getResult();


### Examples
```php

use MeestExpress\MeestExpress;
use MeestExpress\Filter;

$login = 'login';
$pass = 'pass';

$meest_express = new MeestExpress($login, $pass);

// Get Search api 
$meest_search = $meest_express->setFormat('array')->search();

// search country
$filter = new Filter();
$filter->name = 'UKRAI%';
var_dump($meest_search->country($filter)->getResult());

//  search region
$filter = new Filter();
$filter->name = 'Черн%';
$filter->country_id = 'c35b6195-4ea3-11de-8591-001d600938f8'; // Ukraine

var_dump($meest_search->region($filter)->getResult());

//search city
$filter = new Filter();
$filter->name = 'Черн%';
$filter->country_id = 'c35b6195-4ea3-11de-8591-001d600938f8'; // Ukraine
$filter->region_id = 'd15e3031-60b0-11de-be1e-0030485903e8';

var_dump($meest_search->city($filter)->getResult());

//search address
$filter = new Filter();
$filter->name = 'Черн%';
$filter->country_id = 'c35b6195-4ea3-11de-8591-001d600938f8'; // Ukraine
$filter->region_id = 'd15e3031-60b0-11de-be1e-0030485903e8';

var_dump($meest_search->city($filter)->getResult());


```

