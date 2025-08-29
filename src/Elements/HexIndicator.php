<?php

namespace Eazpl\Elements;

use Eazpl\Contracts\RendererInterface;

class HexIndicator implements RendererInterface
{
    /**
     * @param string $text
     */
    public function __construct(protected string $text = '')
    {
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        return sprintf('^FH%s', mb_substr($this->text, 0, 1));
    }
}
