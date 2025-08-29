<?php

namespace Eazpl\Elements;

use Eazpl\Contracts\RendererInterface;

class HorizontalLine implements RendererInterface
{
    /**
     * @param int $width
     * @param int $thickness
     */
    public function __construct(protected int $width, protected int $thickness = 3)
    {
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        $height = 3;

        if ($height < $this->thickness) {
            $height = $this->thickness;
        }

        return (new Box($this->width, $height, $this->thickness))->render();
    }
}
