<?php

namespace Eazpl\Elements;

use Eazpl\Contracts\RendererInterface;
use Eazpl\Utils\RenderUtils;
use Eazpl\Utils\PrinterUtils;

class ReversePrint implements RendererInterface
{
    /**
     * @var array
     */
    protected array $elements = [];

    /**
     * @param RendererInterface ...$elements
     */
    public function __construct(RendererInterface ...$elements)
    {
        $this->elements = $elements;
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        return '^FR' . RenderUtils::renderInsiderElements($this->elements);
    }
}
