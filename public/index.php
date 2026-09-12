<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use Eazpl\App\Bootstrap;
use Eazpl\App\Support\ServerRequestFactory;

$container = Bootstrap::container();
$router = Bootstrap::router($container);
$factory = new \Nyholm\Psr7\Factory\Psr17Factory();
$request = ServerRequestFactory::fromGlobals($factory);

if ($request->getMethod() === 'OPTIONS') {
    $response = $factory->createResponse(204);
} else {
    $response = $router->dispatch($request);
}

http_response_code($response->getStatusCode());
foreach ($response->getHeaders() as $name => $values) {
    foreach ($values as $value) {
        header("{$name}: {$value}", replace: false);
    }
}

echo $response->getBody();
