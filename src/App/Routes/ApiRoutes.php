<?php

namespace Eazpl\App\Routes;

use Eazpl\App\Controllers\ZplComponentController;
use Eazpl\App\Controllers\ZplGenerateController;
use League\Container\Container;
use League\Route\Router;

final class ApiRoutes
{
    public static function register(Router $router, Container $container): Router
    {
        $router->get('/api/zpl/components', $container->get(ZplComponentController::class));
        $router->post('/api/zpl/generate', $container->get(ZplGenerateController::class));

        return $router;
    }
}
