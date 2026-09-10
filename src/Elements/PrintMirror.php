<?php

namespace Eazpl\Elements;

use Eazpl\Contracts\RendererInterface;
use Eazpl\Enums\BoolEnums;
use Eazpl\Utils\RenderUtils;

class PrintMirror implements RendererInterface
{
    public function __construct(protected BoolEnums|string|bool $mirror = false)
    {
        $this->mirror = RenderUtils::getValidBoolean($this->mirror);
    }

    public function render(): string
    {
        return '^PM' . $this->mirror->value;
    }
}
