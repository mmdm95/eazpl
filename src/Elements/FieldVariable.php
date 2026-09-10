<?php

namespace Eazpl\Elements;

use Eazpl\Contracts\RendererInterface;

class FieldVariable implements RendererInterface
{
    public function __construct(protected string $data)
    {
    }

    public function render(): string
    {
        return sprintf('^FV%s', $this->data);
    }
}
