<?php

namespace Eazpl\Elements;

use Eazpl\Contracts\RendererInterface;
use Eazpl\Enums\FieldOrientationEnums;
use Eazpl\Utils\RenderUtils;

class PrintOrientation implements RendererInterface
{
    public function __construct(protected FieldOrientationEnums|string $orientation)
    {
        $this->orientation = RenderUtils::getValidFieldOrientation($this->orientation);
    }

    public function render(): string
    {
        return '^PO' . $this->orientation->value;
    }
}
