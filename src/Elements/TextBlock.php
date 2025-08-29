<?php

namespace Eazpl\Elements;

use Eazpl\Contracts\RendererInterface;
use Eazpl\Utils\RenderUtils;

class TextBlock implements RendererInterface
{
    /**
     * @var array
     */
    protected array $elements = [];

    /**
     * @param Font $font
     * @param RendererInterface ...$elements
     */
    public function __construct(protected Font $font, RendererInterface ...$elements)
    {
        $this->elements = $elements;
    }

    /**
     * @return Font
     */
    public function getFont(): Font
    {
        return $this->font;
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        return
            sprintf('%s^CF%s%s', "\n", $this->font->isForBlock()->render(), "\n") .
            RenderUtils::renderInsiderElements($this->elements);
    }
}
