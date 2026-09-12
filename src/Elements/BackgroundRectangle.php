<?php

namespace Eazpl\Elements;

use Eazpl\Contracts\RendererInterface;

class BackgroundRectangle implements RendererInterface
{
    public function __construct(
        protected ?int $x = null,
        protected ?int $y = null,
        protected ?int $width = null,
        protected ?int $height = null,
        protected ?int $red = null,
        protected ?int $green = null,
        protected ?int $blue = null,
    ) {
    }

    public function render(): string
    {
        $parameters = [$this->x, $this->y, $this->width, $this->height, $this->red, $this->green, $this->blue];

        if (in_array(null, $parameters, true)) {
            return '~BR';
        }

        return '~BR' . implode(',', $parameters);
    }
}
