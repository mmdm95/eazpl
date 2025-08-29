<?php

namespace Eazpl\Elements;

use Eazpl\Contracts\RendererInterface;
use Eazpl\Enums\ColorEnums;
use Eazpl\Enums\LineOrientationEnums;
use Eazpl\Utils\RenderUtils;
use InvalidArgumentException;

class DiagonalLine implements RendererInterface
{
    /**
     * @param int $width
     * @param int $height
     * @param int $thickness
     * @param ColorEnums|string|null $color
     * @param LineOrientationEnums|string|null $orientation
     */
    public function __construct(
        protected int                              $width,
        protected int                              $height,
        protected int                              $thickness = 3,
        protected ColorEnums|string|null           $color = null,
        protected LineOrientationEnums|string|null $orientation = null,
    )
    {
        $this->width = RenderUtils::getValidValue($this->width, 'Width', 3, 32_000);
        $this->height = RenderUtils::getValidValue($this->height, 'Height', 3, 32_000);
        $this->thickness = RenderUtils::getValidValue($this->thickness, 'Thickness', 1, 32_000);
        $this->color = RenderUtils::getValidColor($this->color);
        $this->orientation = RenderUtils::getValidLineOrientation($this->orientation);
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        if (!is_null($this->orientation)) {
            $this->color = ColorEnums::B;
        }

        return sprintf('^GD%d,%d', $this->width, $this->height) .
            (
            $this->color
                ? ',' . $this->color->value .
                (
                !is_null($this->orientation)
                    ? $this->orientation->value
                    : ''
                )
                : ''
            );
    }
}
