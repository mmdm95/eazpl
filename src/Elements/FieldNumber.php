<?php

namespace Eazpl\Elements;

use Eazpl\Contracts\RendererInterface;
use Eazpl\Utils\RenderUtils;

class FieldNumber implements RendererInterface
{
    public function __construct(protected int $number)
    {
        $this->number = RenderUtils::getValidValue($this->number, 'Field number', 1, 9_999);
    }

    public function render(): string
    {
        return sprintf('^FN%d', $this->number);
    }
}
