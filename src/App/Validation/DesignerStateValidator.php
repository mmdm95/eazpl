<?php

namespace Eazpl\App\Validation;

use InvalidArgumentException;
use Respect\Validation\Validator as V;

final class DesignerStateValidator
{
    public function validate(array $state): array
    {
        V::key('label', V::allOf(
            V::key('width', V::numericVal()->between(1, 32000)),
            V::key('height', V::numericVal()->between(1, 32000)),
            V::key('dpi', V::in([203, 300, 600])),
            V::key('orientation', V::in(['portrait', 'landscape']), false),
        ))->key('components', V::arrayVal()->each(V::allOf(
            V::key('id', V::stringType()->length(1, 128)),
            V::key('type', V::stringType()->length(1, 64)),
            V::key('x', V::numericVal()->between(0, 32000)),
            V::key('y', V::numericVal()->between(0, 32000)),
            V::optional(V::key('width', V::numericVal()->between(1, 32000))),
            V::optional(V::key('height', V::numericVal()->between(1, 32000))),
            V::optional(V::key('rotation', V::intVal()->between(0, 359))),
            V::key('attributes', V::arrayVal()),
        )))->key('selectedComponentId', V::nullable(V::stringType()->length(1, 128)))
            ->key('zoom', V::numericVal()->between(0.1, 4))
            ->key('grid', V::allOf(
                V::key('enabled', V::boolVal()),
                V::key('size', V::numericVal()->between(1, 200)),
                V::key('snap', V::boolVal()),
            ))
            ->assert($state);

        return $state;
    }
}
