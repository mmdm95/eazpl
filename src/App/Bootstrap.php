<?php

namespace Eazpl\App;

use Eazpl\App\Controllers\ZplComponentController;
use Eazpl\App\Controllers\ZplGenerateController;
use Eazpl\App\Database\Database;
use Eazpl\App\Middleware\CorsMiddleware;
use Eazpl\App\Middleware\ErrorHandlingMiddleware;
use Eazpl\App\Middleware\JsonBodyMiddleware;
use Eazpl\App\Routes\ApiRoutes;
use Eazpl\App\Services\ZplComponentRegistry;
use Eazpl\App\Services\ZplDesignerGenerator;
use Eazpl\App\Validation\ComponentAttributeValidator;
use Eazpl\App\Validation\DesignerStateValidator;
use League\Container\Container;
use League\Route\Router;

final class Bootstrap
{
    public static function container(): Container
    {
        $container = new Container();

        $container->add(ZplComponentRegistry::class, static fn (): ZplComponentRegistry => new ZplComponentRegistry());
        $container->add(
            ZplDesignerGenerator::class,
            static fn (): ZplDesignerGenerator => new ZplDesignerGenerator(
                $container->get(ZplComponentRegistry::class),
            ),
        );
        $container->add(DesignerStateValidator::class);
        $container->add(ComponentAttributeValidator::class);
        $container->add(
            ZplComponentController::class,
            static fn (): ZplComponentController => new ZplComponentController(
                $container->get(ZplComponentRegistry::class),
            ),
        );
        $container->add(
            ZplGenerateController::class,
            static fn (): ZplGenerateController => new ZplGenerateController(
                $container->get(ZplComponentRegistry::class),
                $container->get(ZplDesignerGenerator::class),
                $container->get(DesignerStateValidator::class),
                $container->get(ComponentAttributeValidator::class),
            ),
        );

        if (class_exists(Database::class) && getenv('EAZPL_DB_ENABLED') === 'true') {
            Database::boot();
        }

        return $container;
    }

    public static function router(Container $container): Router
    {
        $router = new Router();
        $router->middleware(new ErrorHandlingMiddleware());
        $router->middleware(new CorsMiddleware());
        $router->middleware(new JsonBodyMiddleware());

        return ApiRoutes::register($router, $container);
    }
}
