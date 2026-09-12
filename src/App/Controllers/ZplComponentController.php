<?php

namespace Eazpl\App\Controllers;

use Eazpl\App\Services\ZplComponentRegistry;
use Eazpl\App\Support\JsonResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class ZplComponentController
{
    public function __construct(private readonly ZplComponentRegistry $registry)
    {
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        return JsonResponse::success($this->registry->all());
    }
}
