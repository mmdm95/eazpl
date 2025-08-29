<?php

namespace Eazpl\Elements;

use Eazpl\Contracts\RendererInterface;
use Eazpl\Elements\Grouping\GroupTextWrapper;
use Eazpl\Utils\Utils;

class TextGroup implements RendererInterface
{
    /**
     * @var array
     */
    protected array $texts = [];

    /**
     * @var Font
     */
    protected Font $font;

    /**
     * @var int
     */
    protected int $maxX;

    /**
     * @var int
     */
    protected int $maxY;

    /**
     * Define a character width ratio based on the font height
     *
     * @var float
     */
    protected float $charWidthRatio = 0.6; // This can vary based on the font used

    /**
     * @param int $x
     * @param int $y
     * @param string $orientation
     * @param GroupTextWrapper|Text ...$texts
     */
    public function __construct(
        protected int           $x,
        protected int           $y,
        protected string        $orientation = 'h',
        protected int           $gap = 5,
        GroupTextWrapper|Text   ...$texts
    )
    {
        $this->orientation = in_array($orientation, ['h', 'v']) ? $orientation : 'h';
        $this->texts = array_filter(
            $texts,
            fn($text) => $text instanceof Text || ($text instanceof GroupTextWrapper && $text->getOrientation() === 'v')
        );

        $this->maxX = $this->x;
        $this->maxY = $this->y;
        $this->font = new Font('A', 30);
    }

    /**
     * @param Font $font
     * @return static
     */
    public function font(Font $font): static
    {
        $this->font = $font;
        return $this;
    }

    /**
     * @return array
     */
    public function getTexts(): array
    {
        return $this->texts;
    }

    /**
     * @return string
     */
    public function getOrientation(): string
    {
        return $this->orientation;
    }

    /**
     * @return int
     */
    public function getMaxX(): int
    {
        return $this->maxX;
    }

    /**
     * @return int
     */
    public function getMaxY(): int
    {
        return $this->maxY;
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        $zpl = [];
        $cursorX = $this->x;
        $cursorY = $this->y;

        foreach ($this->texts as $text) {
            if ($text instanceof GroupTextWrapper) {
                $groupMaxW = $text->getMaxW();
                $groupMaxH = $text->getMaxH();

                // Render at current position
                $element = new TextGroup($cursorX, $cursorY, $text->getOrientation(), $text->getGap(), ...$text->getTexts());
                $zpl[] = $element->render();

                // Move cursor **after rendering**
                if ($this->orientation === 'h') {
                    $cursorX += $groupMaxW + $text->getGap();
                } else {
                    $cursorY += $groupMaxH + $text->getGap();
                }
            } else {
                $font = $text->getFont() ?? $this->font;
                $textWidth = Utils::estimateStringWidth($font, $text->getText(), $this->charWidthRatio);
                $textHeight = $font->getHeight();

                // Render at current position
                $element = new Position($cursorX, $cursorY, $text);
                $zpl[] = $element->render();

                // Move cursor **after rendering**
                if ($this->orientation === 'h') {
                    $cursorX += $textWidth + $this->gap;
                } else {
                    $cursorY += $textHeight + $this->gap;
                }
            }
        }

        $this->maxX = $cursorX;
        $this->maxY = $cursorY;

        return implode('', $zpl);
    }
}
