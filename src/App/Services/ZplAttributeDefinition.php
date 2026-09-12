<?php

namespace Eazpl\App\Services;

use JsonSerializable;

final class ZplAttributeDefinition implements JsonSerializable
{
    public function __construct(
        public readonly string $name,
        public readonly string $type,
        public readonly string $label,
        public readonly bool $required = false,
        public readonly mixed $default = null,
        public readonly array $options = [],
    ) {
    }

    public function jsonSerialize(): array
    {
        $data = [
            'name' => $this->name,
            'type' => $this->type,
            'label' => $this->label,
            'required' => $this->required,
            'default' => $this->default,
        ];

        if ($this->options !== []) {
            $data['options'] = $this->options;
        }

        return $data;
    }
}
