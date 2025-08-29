<?php

namespace Eazpl\Elements;

use Eazpl\Contracts\RendererInterface;

class Comment implements RendererInterface
{
    /**
     * @param string $comment
     */
    public function __construct(protected string $comment)
    {
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        return sprintf('^FX %s%s', $this->comment, "\n");
    }
}
