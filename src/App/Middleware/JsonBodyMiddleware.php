<?php

namespace Eazpl\App\Middleware;

use JsonException;
use Nyholm\Psr7\Stream;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class JsonBodyMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!in_array(strtoupper($request->getMethod()), ['POST', 'PUT', 'PATCH'], true)) {
            return $handler->handle($request);
        }

        $contentType = strtolower($request->getHeaderLine('Content-Type'));

        if (!str_contains($contentType, 'application/json')) {
            return $handler->handle($request);
        }

        $body = (string)$request->getBody();

        if ($body === '') {
            return $handler->handle($request->withParsedBody([]));
        }

        try {
            $decoded = json_decode($body, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new JsonException('The request body contains invalid JSON.', 0, $exception);
        }

        return $handler->handle($request->withParsedBody($decoded));
    }
}
