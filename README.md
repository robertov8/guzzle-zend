# Guzzle - Zend Http
:metal: Guzzle to Zend Http Adapter, justa change you instance ;)  

O pacote está disponivel somente no metodo $client->request(...), as demais funções e novas implementações estão em aberto via GitHub.

Basta utilizar, de forma identica a utilização do Guzzle.
## Zend Http + Adapter
```php
<?php
use ZendAdapter\ZendRequest as Client;

require __DIR__ . '/vendor/autoload.php';
$client = new Client([
    'base_uri' => 'http://httpbin.org',
    'timeout'  => 2.0,
]);

$response = $client->request('GET', '/root');
$response = $client->request('GET', '/root');
$responseBody = $response->getBody();
$responseBodyContents = $responseBody->getContents();
```
## Guzzle
```php
<?php
use GuzzleHttp\Client;

require __DIR__ . '/vendor/autoload.php';
$client = new Client([
    'base_uri' => 'http://httpbin.org',
    'timeout'  => 2.0,
]);

$response = $client->request('GET', '/root');
$responseBody = $response->getBody();
$responseBodyContents = $responseBody->getContents();

```
