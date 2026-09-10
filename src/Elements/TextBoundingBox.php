<?php

namespace Eazpl\Elements;

use Eazpl\Contracts\RendererInterface;
use Eazpl\Enums\FieldOrientationEnums;
use Eazpl\Utils\RenderUtils;

class TextBoundingBox implements RendererInterface
{
    /**
     * @var array<RendererInterface>
     */
    protected array $elements = [];

    public function __construct(
        protected int                          $maxWidth,
        protected int                          $maxHeight,
        protected FieldOrientationEnums|string $orientation = FieldOrientationEnums::_0,
        RendererInterface                      ...$elements
    )
    {
        $this->maxWidth = RenderUtils::getValidValue($this->maxWidth, 'Maximum width', 1, 32_000);
        $this->maxHeight = RenderUtils::getValidValue($this->maxHeight, 'Maximum height', 1, 32_000);
        $this->orientation = RenderUtils::getValidFieldOrientation($this->orientation);
        $this->elements = $elements;
    }

    public function render(): string
    {
        return sprintf(
            '^TB%s,%d,%d',
            $this->orientation->value,
            $this->maxWidth,
            $this->maxHeight
        ) . RenderUtils::renderInsiderElements($this->elements);
    }
}
