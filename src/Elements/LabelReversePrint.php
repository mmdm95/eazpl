<?php

namespace Eazpl\Elements;

use Eazpl\Contracts\RendererInterface;
use Eazpl\Enums\BoolEnums;
use Eazpl\Utils\RenderUtils;

class LabelReversePrint implements RendererInterface
{
    public function __construct(protected BoolEnums|string|bool $reverse = false)
    {
        $this->reverse = RenderUtils::getValidBoolean($this->reverse);
    }

    public function render(): string
    {
        return '^LR' . $this->reverse->value;
    }
}
