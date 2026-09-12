<?php

namespace Eazpl\Elements;

use Eazpl\Contracts\RendererInterface;

class Break_ implements RendererInterface
{
    public function render(): string
    {
        return '~BR';
    }
}
