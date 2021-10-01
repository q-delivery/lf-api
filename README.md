# lf-api

| Branch    | PHP                                         | Code Coverage                                        |
|-----------|---------------------------------------------|------------------------------------------------------|
| `master`  | [![PHP][build-status-master-php]][actions]  | [![Code Coverage][coverage-status-master]][codecov]  |
| `develop` | [![PHP][build-status-develop-php]][actions] | [![Code Coverage][coverage-status-develop]][codecov] |

## Usage

### Setup
```php
use Gansel\LF\Api\FallApi;
use Symfony\Component\HttpClient\HttpClient;

$baseUri = 'https://....';
$username = '...';
$password = '...';

$client = HttpClient::create([
    'auth_basic' => [$username, $password],
    'max_duration' => 0,
]);

$fallApi = new FallApi($baseUri, $client);
```

### Create Fall
```php
use Gansel\LF\Api\FallApi;

$fallApi = new FallApi(/* ... */);
$fallApi->create($payload);
```

### Get Fall
```php
use Gansel\LF\Api\Domain\Value\Fall\FallUuid;
use Gansel\LF\Api\FallApi;

$fallApi = new FallApi(/* ... */);

$fallApi->get(
    FallUuid::fromString('123-456-789'), // the Fall UUID
);
```


### Update Fall

**Only works before calling a Transition!**

```php
use Gansel\LF\Api\Domain\Value\Fall\FallUuid;
use Gansel\LF\Api\FallApi;

$fallApi = new FallApi(/* ... */);

$fallApi->update(
    FallUuid::fromString('123-456-789'), // the Fall UUID
    [
        'field' => 'value',
        // ...
    ]
);
```

### Set Leadsale values

```php
use Gansel\LF\Api\Domain\Value\Fall\FallUuid;
use Gansel\LF\Api\FallApi;

$fallApi = new FallApi(/* ... */);

$now = new DateTime();

$fallApi->updateLeadsaleValues(
    FallUuid::fromString('123-456-789'), // the Fall UUID
    true, // or false, the decision by the User
    $now, // a \DateTimeInterface, when the decision was made by the User
);
```

### Upload File to Fall
```php
use Gansel\LF\Api\Domain\Value\Fall\FallUuid;
use Gansel\LF\Api\FallApi;

$fallApi = new FallApi(/* ... */);

$fallApi->uploadFile(
    FallUuid::fromString('123-456-789'), // the Fall UUID
    '/var/test/testfile.txt',            // use the absolute filepath
    'Fahrzeugschein',                    // a prefix which can be added to the filename
    false                                // wether this file should be marked as new in LF or not
);
```

### Apply Transition
```php
use Gansel\LF\Api\Domain\Value\Fall\FallUuid;
use Gansel\LF\Api\FallApi;

$fallApi = new FallApi(/* ... */);

$fallApi->applyTransition(
    FallUuid::fromString('123-456-789'), // the Fall UUID
    'einreichen'                         // the transition which should be applied
);
```

---
**NOTE**

`create()` and `get()` already return a `FallUuid` which can be used to upload a file or apply a transition!

---

[build-status-develop-php]: https://github.com/gansel-rechtsanwaelte/lf-api/workflows/PHP/badge.svg?branch=develop
[build-status-master-php]: https://github.com/gansel-rechtsanwaelte/lf-api/workflows/PHP/badge.svg?branch=master
[coverage-status-develop]: https://codecov.io/gh/gansel-rechtsanwaelte/lf-api/branch/develop/graph/badge.svg
[coverage-status-master]: https://codecov.io/gh/gansel-rechtsanwaelte/lf-api/branch/master/graph/badge.svg

[actions]: https://github.com/gansel-rechtsanwaelte/lf/actions
[codecov]: https://codecov.io/gh/gansel-rechtsanwaelte/lf
