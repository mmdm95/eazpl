<?php

namespace Eazpl\App\Controllers;

use Eazpl\App\Requests\GenerateZplRequest;
use Eazpl\App\Services\ZplComponentRegistry;
use Eazpl\App\Services\ZplDesignerGenerator;
use Eazpl\App\Services\ZplPreviewRenderer;
use Eazpl\App\Support\JsonResponse;
use Eazpl\App\Validation\ComponentAttributeValidator;
use Eazpl\App\Validation\DesignerStateValidator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Throwable;

final class ZplGenerateController
{
    public function __construct(
        private readonly ZplComponentRegistry $registry,
        private readonly ZplDesignerGenerator $generator,
        private readonly DesignerStateValidator $stateValidator,
        private readonly ComponentAttributeValidator $attributeValidator,
        private readonly ZplPreviewRenderer $previewRenderer,
    ) {
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $state = $this->stateValidator->validate(
            GenerateZplRequest::fromServerRequest($request)->state,
        );

        foreach ($state['components'] as $instance) {
            $definition = $this->registry->get($instance['type']);
            $attributes = $definition->withDefaults($instance['attributes']);

            foreach ($definition->attributes as $attributeDefinition) {
                $this->attributeValidator->validate(
                    $attributeDefinition,
                    $attributes[$attributeDefinition->name] ?? null,
                );
            }
        }

        $zpl = $this->generator->generate($state);
        $preview = null;
        $previewError = null;

        try {
            $preview = $this->previewRenderer->render($zpl, $state['label']);
        } catch (Throwable $previewException) {
            $previewError = $previewException->getMessage();
        }

        return JsonResponse::success(['zpl' => $zpl, 'preview' => $preview, 'previewError' => $previewError]);
    }
}
