<?php

namespace Eazpl\Elements;

use Eazpl\Contracts\RendererInterface;
use Eazpl\Enums\ColorEnums;
use Eazpl\Utils\RenderUtils;
use InvalidArgumentException;

class Box implements RendererInterface
{
    /**
     * @param int $width
     * @param int $height
     * @param int $thickness
     * @param ColorEnums|string|null $color
     * @param int|null $rounding
     */
    public function __construct(
        protected int                    $width,
        protected int                    $height,
        protected int                    $thickness = 3,
        protected ColorEnums|string|null $color = null,
        protected ?int                   $rounding = null
    )
    {
        $this->width = RenderUtils::getValidValue($this->width, 'Width', max: 32_000);
        $this->height = RenderUtils::getValidValue($this->height, 'Height', max: 32_000);
        $this->thickness = RenderUtils::getValidValue($this->thickness, 'Thickness', 1, 32_000);

        if ($this->width < $this->thickness || $this->height < $this->thickness) {
            throw new InvalidArgumentException(
                ($this->width < $this->thickness ? 'Width' : 'Height') .
                ' must be greater than or equal to ' . $this->thickness
            );
        }

        $this->color = RenderUtils::getValidColor($this->color);

        if (!is_null($this->rounding) && ($this->rounding < 0 || $this->rounding > 8)) {
            throw new InvalidArgumentException('Rounding value must be between 0 and 8');
        }
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        if (!is_null($this->rounding)) {
            $this->color = ColorEnums::B;
        }

        return sprintf('^GB%d,%d,%d', $this->width, $this->height, $this->thickness) .
            (
            $this->color
                ? ',' . $this->color->value .
                (
                !is_null($this->rounding)
                    ? ',' . $this->rounding
                    : ''
                )
                : ''
            );
    }
}
