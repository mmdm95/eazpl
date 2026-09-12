<?php

namespace Eazpl\App\Validation;

use Eazpl\App\Services\ZplAttributeDefinition;
use InvalidArgumentException;
use Respect\Validation\Validator as V;

final class ComponentAttributeValidator
{
    public function validate(ZplAttributeDefinition $definition, mixed $value): void
    {
        if ($value === null || $value === '') {
            if ($definition->required) {
                throw new InvalidArgumentException("The {$definition->label} field is required.");
            }

            return;
        }

        $label = $definition->label;

        match ($definition->type) {
            'string' => V::stringType()->setName($label)->assert($value),
            'text' => V::stringType()->setName($label)->assert($value),
            'number' => V::numericVal()->setName($label)->assert($value),
            'boolean' => V::boolVal()->setName($label)->assert($value),
            'select' => V::in($this->optionValues($definition), true)->setName($label)->assert($value),
            'image' => V::stringType()->startsWith('data:image/')->setName($label)->assert($value),
            'json' => $this->validateJson($label, $value),
            default => throw new InvalidArgumentException("Unsupported attribute type: {$definition->type}."),
        };
    }

    private function validateJson(string $label, mixed $value): void
    {
        if (is_array($value)) {
            return;
        }

        V::stringType()->setName($label)->assert($value);
        json_decode($value, true, 512, JSON_THROW_ON_ERROR);
    }

    private function optionValues(ZplAttributeDefinition $definition): array
    {
        return array_map(
            static fn (array $option): mixed => $option['value'],
            $definition->options,
        );
    }
}
