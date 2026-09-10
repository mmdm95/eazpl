<?php

namespace Eazpl\Elements;

use Eazpl\Contracts\RendererInterface;
use Eazpl\Utils\RenderUtils;

class LabelHome implements RendererInterface
{
    public function __construct(protected int $x = 0, protected int $y = 0)
    {
        $this->x = RenderUtils::getValidXYValue($this->x);
        $this->y = RenderUtils::getValidXYValue($this->y);
    }

    public function render(): string
    {
        return sprintf('^LH%d,%d', $this->x, $this->y);
    }
}
