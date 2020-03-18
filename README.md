# lf-api

| Branch    | PHP                                         |
|-----------|---------------------------------------------|
| `master`  | [![PHP][build-status-master-php]][actions]  |
| `develop` | [![PHP][build-status-develop-php]][actions] |

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

### Upload File to Fall
```php
use Gansel\LF\Api\Domain\Value\Fall\FallUuid;
use Gansel\LF\Api\FallApi;

$fallApi = new FallApi(/* ... */);

$fallApi->uploadFile(
    FallUuid::fromString('123-456-789'), // the Fall UUID
    '/var/test/testfile.txt',            // use the absolute filepath
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

[actions]: https://github.com/gansel-rechtsanwaelte/lf/actions
