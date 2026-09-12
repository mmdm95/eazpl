<?php

namespace Eazpl\App\Support;

use JsonSerializable;
use Nyholm\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

final class JsonResponse
{
    public static function make(mixed $data, int $status = 200): ResponseInterface
    {
        return new Response(
            $status,
            ['Content-Type' => 'application/json; charset=utf-8'],
            self::encode($data),
        );
    }

    public static function success(mixed $data): ResponseInterface
    {
        return self::make(['data' => $data]);
    }

    public static function error(string $message, int $status, array $errors = []): ResponseInterface
    {
        $payload = ['error' => ['message' => $message]];

        if ($errors !== []) {
            $payload['error']['errors'] = $errors;
        }

        return self::make($payload, $status);
    }

    private static function encode(mixed $data): string
    {
        $encoded = json_encode(
            $data instanceof JsonSerializable ? $data->jsonSerialize() : $data,
            JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR,
        );

        return $encoded . "\n";
    }
}
