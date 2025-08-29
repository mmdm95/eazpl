<?php

namespace Eazpl\Elements;

use Eazpl\Contracts\RendererInterface;

class Raw implements RendererInterface
{
    /**
     * @param string $raw
     */
    public function __construct(protected string $raw)
    {
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        return $this->raw;
    }
}
