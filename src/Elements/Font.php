<?php

namespace Eazpl\Elements;

use Eazpl\Contracts\RendererInterface;
use Eazpl\Enums\FieldOrientationEnums;
use Eazpl\Utils\RenderUtils;
use InvalidArgumentException;

class Font implements RendererInterface
{
    /**
     * @var bool
     */
    protected bool $isForBlock = false;

    /**
     * @var string
     */
    protected string $defaultFontPath;

    /**
     * @param string $fontName
     * @param int $height
     * @param int|null $width
     * @param string|null $fontFacePath
     */
    public function __construct(
        protected string                            $fontName,
        protected int                               $height,
        protected ?int                              $width = null,
        protected FieldOrientationEnums|string|null $orientation = null,
        protected ?string                           $fontFacePath = null
    )
    {
        $this->fontName = trim($this->fontName);

        if (!preg_match('/^[0-9A-Z]$/', $this->fontName)) {
            throw new InvalidArgumentException('Font name must be 0-9 or A-Z');
        }

        if ($this->orientation) {
            $this->orientation = RenderUtils::getValidFieldOrientation($this->orientation);
        }

        if ($this->fontFacePath && !file_exists($this->fontFacePath)) {
            throw new InvalidArgumentException('Please provide a valid font face path');
        }

        $this->defaultFontPath = dirname(__DIR__) . '/Fonts/IRANSansWeb.ttf';
    }

    /**
     * @return int
     */
    public function getHeight(): int
    {
        return $this->height;
    }

    /**
     * @return int|null
     */
    public function getWidth(): ?int
    {
        return $this->width;
    }

    /**
     * @return float
     */
    public function getRatio(): float
    {
        if (!$this->width || $this->width < $this->height) {
            return 1.0;
        }

        return (float)$this->width / (float)$this->height;
    }

    /**
     * @return string
     */
    public function getFontFace(): string
    {
        return $this->fontFacePath ?? $this->defaultFontPath;
    }

    /**
     * @param bool $bool
     * @return static
     */
    public function isForBlock(bool $bool = true): static
    {
        $this->isForBlock = $bool;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        return (!$this->isForBlock ? '^A' : '') .
            $this->fontName .
            ($this->orientation ? $this->orientation->value : '') .
            ',' .
            $this->height .
            ($this->width ? ',' . $this->width : '');
    }
}
