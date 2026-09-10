<?php

namespace Eazpl\Elements;

use Eazpl\Contracts\RendererInterface;
use Eazpl\Utils\RenderUtils;

class PrintWidth implements RendererInterface
{
    public function __construct(protected int $width)
    {
        $this->width = RenderUtils::getValidValue($this->width, 'Width', 1, 32_000);
    }

    public function render(): string
    {
        return sprintf('^PW%d', $this->width);
    }
}
