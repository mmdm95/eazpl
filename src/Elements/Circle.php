<?php

namespace Eazpl\Elements;

use Eazpl\Contracts\RendererInterface;
use Eazpl\Enums\ColorEnums;
use Eazpl\Utils\RenderUtils;

class Circle implements RendererInterface
{
    /**
     * @param int $diameter
     * @param int $thickness
     * @param ColorEnums $color
     */
    public function __construct(
        protected int        $diameter,
        protected int        $thickness = 3,
        protected ColorEnums $color = ColorEnums::B
    )
    {
        $this->diameter = RenderUtils::getValidValue($this->diameter, 'Diameter', 3, 4_095);
        $this->thickness = RenderUtils::getValidValue($this->thickness, 'Thickness', 1, 4_095);
    }

    /**
     * @return $this
     */
    public function fill(): static
    {
        $this->thickness = $this->diameter;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        return sprintf('^GC%d,%d,%s', $this->diameter, $this->thickness, $this->color->value);
    }
}
