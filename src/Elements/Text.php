<?php

namespace Eazpl\Elements;

use Eazpl\Contracts\RendererInterface;

class Text implements RendererInterface
{
    /**
     * @param string $text
     * @param Font|null $font
     */
    public function __construct(protected string $text, protected ?Font $font = null)
    {
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @return Font|null
     */
    public function getFont(): ?Font
    {
        return $this->font;
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        return ($this->font ? $this->font->render() : '') . sprintf('^FD%s', $this->text);
    }
}
