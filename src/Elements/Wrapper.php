<?php

namespace Eazpl\Elements;

use Eazpl\Contracts\RendererInterface;
use Eazpl\Utils\RenderUtils;

class Wrapper implements RendererInterface
{
    /**
     * @var array<RendererInterface>
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
     * @return array<RendererInterface>
     */
    public function getElements(): array
    {
        return $this->elements;
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        return RenderUtils::renderInsiderElements($this->elements);
    }
}
