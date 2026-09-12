<?php

namespace Eazpl\App\Support;

use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Uri;
use Psr\Http\Message\ServerRequestInterface;

final class ServerRequestFactory
{
    public static function fromGlobals(Psr17Factory $factory): ServerRequestInterface
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        $headers = [];

        foreach ($_SERVER as $key => $value) {
            $key = (string)$key;

            if (str_starts_with($key, 'HTTP_')) {
                $name = strtolower(str_replace('_', '-', substr($key, 5)));
                $headers[$name] = (array)$value;
            } elseif (in_array($key, ['CONTENT_TYPE', 'CONTENT_LENGTH'], true)) {
                $name = strtolower(str_replace('_', '-', $key));
                $headers[$name] = (array)$value;
            }
        }

        $request = $factory
            ->createServerRequest($method, new Uri("{$scheme}://{$host}{$path}"), $_SERVER)
            ->withBody($factory->createStreamFromFile('php://input'));

        foreach ($headers as $name => $values) {
            $request = $request->withHeader($name, $values);
        }

        $protocol = (string)($_SERVER['SERVER_PROTOCOL'] ?? '1.1');

        return $request->withProtocolVersion(str_replace('HTTP/', '', $protocol));
    }
}
