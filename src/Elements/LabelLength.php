<?php

namespace Eazpl\Elements;

use Eazpl\Contracts\RendererInterface;
use Eazpl\Enums\BoolEnums;
use Eazpl\Utils\RenderUtils;

class LabelLength implements RendererInterface
{
    public function __construct(
        protected int                           $length,
        protected BoolEnums|string|bool|null    $allMedia = null
    )
    {
        $this->length = RenderUtils::getValidValue($this->length, 'Length', 1, 32_000);
        $this->allMedia = RenderUtils::getValidBoolean($this->allMedia);
    }

    public function render(): string
    {
        return sprintf('^LL%d', $this->length) .
            ($this->allMedia ? ',' . $this->allMedia->value : '');
    }
}
