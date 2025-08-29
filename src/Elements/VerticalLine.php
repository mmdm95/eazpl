<?php

namespace Eazpl\Elements;

use Eazpl\Contracts\RendererInterface;

class VerticalLine implements RendererInterface
{
    /**
     * @param int $height
     * @param int $thickness
     */
    public function __construct(protected int $height, protected int $thickness = 3)
    {
        if ($this->height < $this->thickness) {
            $this->height = $this->thickness;
        }
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        $width = 3;

        if ($width < $this->thickness) {
            $width = $this->thickness;
        }

        return (new Box($width, $this->height, $this->thickness))->render();
    }
}
