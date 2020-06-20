
## Meest Express API v3



### Composer
composer require yr4ik/meest-express-api-v3


### Example
```php

use MeestExpress\MeestExpress;
use MeestExpress\Filter;

$login = 'login';
$pass = 'pass';

$meest_express = new MeestExpress($login, $pass);
$meest_search = $meest_express->setFormat('json')->search();

$filter = new Filter();
$filter->name = 'UKRAI%';
var_dump($meest_search->country($filter));


$filter = new Filter();
$filter->name = 'Черн%';
$filter->country_id = 'c35b6195-4ea3-11de-8591-001d600938f8'; // Ukraine

var_dump($meest_search->region($filter)->getResult());

$filter = new Filter();
$filter->name = 'Черн%';
$filter->country_id = 'c35b6195-4ea3-11de-8591-001d600938f8'; // Ukraine
$filter->region_id = 'd15e3031-60b0-11de-be1e-0030485903e8';

var_dump($meest_search->city($filter)->getResult());


```

