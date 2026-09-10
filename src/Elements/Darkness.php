<?php

namespace Eazpl\Elements;

use Eazpl\Contracts\RendererInterface;
use Eazpl\Utils\RenderUtils;

class Darkness implements RendererInterface
{
    public function __construct(protected int $modifier)
    {
        $this->modifier = RenderUtils::getValidValue($this->modifier, 'Darkness modifier', -30, 30);
    }

    public function render(): string
    {
        return sprintf('^MD%d', $this->modifier);
    }
}
