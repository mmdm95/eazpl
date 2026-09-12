<?php

namespace Eazpl\App\Middleware;

use Eazpl\App\Support\JsonResponse;
use InvalidArgumentException;
use JsonException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Exceptions\ValidationException;
use RuntimeException;
use Throwable;

final class ErrorHandlingMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (NestedValidationException $exception) {
            return JsonResponse::error('The supplied designer state is invalid.', 422, [
                'validation' => $exception->getMessages(),
            ]);
        } catch (ValidationException $exception) {
            return JsonResponse::error($exception->getMessage(), 422);
        } catch (InvalidArgumentException | JsonException $exception) {
            return JsonResponse::error($exception->getMessage(), 422);
        } catch (Throwable $exception) {
            $message = $exception instanceof RuntimeException
                ? $exception->getMessage()
                : 'Unable to process the ZPL request.';

            return JsonResponse::error($message, 500);
        }
    }
}
