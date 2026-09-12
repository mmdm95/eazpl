<?php

namespace Eazpl\App\Services;

use Eazpl\Contracts\RendererInterface;
use JsonSerializable;

final class ZplComponentDefinition implements JsonSerializable
{
    /**
     * @param list<ZplAttributeDefinition> $attributes
     * @param callable(array<string, mixed>): RendererInterface $factory
     */
    public function __construct(
        public readonly string $type,
        public readonly string $name,
        public readonly string $icon,
        public readonly string $description,
        public readonly string $category,
        public readonly string $preview,
        public readonly array $attributes,
        public readonly mixed $factory,
        public readonly int $defaultWidth = 100,
        public readonly int $defaultHeight = 30,
        public readonly bool $resizable = true,
        public readonly bool $rotatable = true,
    ) {
    }

    public function create(array $instance): RendererInterface
    {
        $instance['attributes'] = $this->withDefaults($instance['attributes'] ?? []);

        return ($this->factory)($instance);
    }

    public function withDefaults(array $attributes): array
    {
        foreach ($this->attributes as $definition) {
            if (!array_key_exists($definition->name, $attributes) && $definition->default !== null) {
                $attributes[$definition->name] = $definition->default;
            }
        }

        return $attributes;
    }

    public function jsonSerialize(): array
    {
        return [
            'type' => $this->type,
            'name' => $this->name,
            'icon' => $this->icon,
            'description' => $this->description,
            'category' => $this->category,
            'preview' => $this->preview,
            'attributes' => $this->attributes,
            'defaultWidth' => $this->defaultWidth,
            'defaultHeight' => $this->defaultHeight,
            'resizable' => $this->resizable,
            'rotatable' => $this->rotatable,
        ];
    }
}
