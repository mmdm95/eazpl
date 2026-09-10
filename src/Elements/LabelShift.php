<?php

namespace Eazpl\Elements;

use Eazpl\Contracts\RendererInterface;
use Eazpl\Utils\RenderUtils;

class LabelShift implements RendererInterface
{
    public function __construct(protected int $distance = 0)
    {
        $this->distance = RenderUtils::getValidXYValue($this->distance);
    }

    public function render(): string
    {
        return sprintf('^LS%d', $this->distance);
    }
}
