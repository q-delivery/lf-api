### How to use

```php
use Gansel\LF\Api\FallApi;
use Symfony\Component\HttpClient\HttpClient;

$baseUri = 'https://....';
$username = '...';
$password = '...';

$client = HttpClient::create([
    'auth_basic' => [$username, $password],
]);

$fallApi = new FallApi($client, $baseUri);
$fallApi->add(/* ... */);
```
