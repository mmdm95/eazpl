<?php

namespace Eazpl\Elements;

use Eazpl\Contracts\RendererInterface;
use Eazpl\Utils\RenderUtils;
use InvalidArgumentException;

class DataMatrix implements RendererInterface
{
    public function __construct(
        protected int         $x,
        protected int         $y,
        protected Text|string $data,
        protected int         $height = 10,
        protected int         $quality = 200
    )
    {
        $this->x = RenderUtils::getValidXYValue($this->x);
        $this->y = RenderUtils::getValidXYValue($this->y);
        $this->height = RenderUtils::getValidValue($this->height, 'Height', 1, 32_000);

        if ($this->quality !== 200) {
            throw new InvalidArgumentException('Labelary supports Data Matrix quality 200 only.');
        }
    }

    public function render(): string
    {
        $data = $this->data instanceof Text ? $this->data : new Text($this->data);

        return (new Position(
            $this->x,
            $this->y,
            new Raw(sprintf('^BXN,%d,%d', $this->height, $this->quality)),
            $data
        ))->render();
    }
}
