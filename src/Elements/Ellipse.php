<?php

namespace Eazpl\Elements;

use Eazpl\Contracts\RendererInterface;
use Eazpl\Enums\ColorEnums;
use Eazpl\Utils\RenderUtils;

class Ellipse implements RendererInterface
{
    /**
     * @param int $width
     * @param int $height
     * @param int $thickness
     * @param ColorEnums|null $color
     */
    public function __construct(
        protected int         $width,
        protected int         $height,
        protected int         $thickness = 3,
        protected ?ColorEnums $color = null
    )
    {
        $this->width = RenderUtils::getValidValue($this->width, 'Width', 3, 4_095);
        $this->height = RenderUtils::getValidValue($this->height, 'Height', 3, 4_095);
        $this->thickness = RenderUtils::getValidValue($this->thickness, 'Thickness', 1, 4_095);
        $this->color = RenderUtils::getValidColor($this->color);
    }

    /**
     * @return $this
     */
    public function fill(): static
    {
        $this->thickness = min($this->width, $this->height);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        return sprintf('^GE%d,%d,%d', $this->width, $this->height, $this->thickness) .
            ($this->color ? ',' . $this->color->value : '');
    }
}
