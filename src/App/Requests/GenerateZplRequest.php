<?php

namespace Eazpl\App\Requests;

use JsonException;
use Psr\Http\Message\ServerRequestInterface;

final class GenerateZplRequest
{
    private function __construct(public readonly array $state)
    {
    }

    public static function fromServerRequest(ServerRequestInterface $request): self
    {
        $body = $request->getParsedBody();

        if (!is_array($body)) {
            throw new JsonException('The request body must contain a designer state object.');
        }

        return new self($body);
    }
}
