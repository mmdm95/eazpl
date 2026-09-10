<?php

namespace Eazpl\Components;

use Eazpl\Contracts\RendererInterface;
use Eazpl\Elements\Box;
use Eazpl\Elements\Position;
use Eazpl\Utils\RenderUtils;
use InvalidArgumentException;

class PlusMinus implements RendererInterface
{
    /**
     * @param int $x
     * @param int $y
     * @param int $size
     * @param int $thickness
     * @param int $gap
     */
    public function __construct(
        protected int $x,
        protected int $y,
        protected int $size = 20,
        protected int $thickness = 3,
        protected int $gap = 3
    )
    {
        $this->x = RenderUtils::getValidXYValue($this->x);
        $this->y = RenderUtils::getValidXYValue($this->y);
        $this->size = RenderUtils::getValidValue($this->size, 'Size', 1, 32_000);
        $this->thickness = RenderUtils::getValidValue($this->thickness, 'Thickness', 1, 32_000);
        $this->gap = RenderUtils::getValidValue($this->gap, 'Gap', 0, 32_000);

        if ($this->size < $this->thickness) {
            throw new InvalidArgumentException('Size must be greater than or equal to thickness');
        }
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        $centerOffset = intdiv($this->size - $this->thickness, 2);

        return (new Position(
                $this->x + $centerOffset,
                $this->y,
                new Box($this->thickness, $this->size, $this->thickness)
            ))->render() .
            (new Position(
                $this->x,
                $this->y + $centerOffset,
                new Box($this->size, $this->thickness, $this->thickness)
            ))->render() .
            (new Position(
                $this->x,
                $this->y + $this->size + $this->gap,
                new Box($this->size, $this->thickness, $this->thickness)
            ))->render();
    }
}
