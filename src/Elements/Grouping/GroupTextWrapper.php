<?php

namespace Eazpl\Elements\Grouping;

use Eazpl\Elements\Font;
use Eazpl\Elements\Text;
use Eazpl\Utils\Utils;

class GroupTextWrapper
{
    /**
     * @var array<Text>
     */
    protected array $texts = [];

    /**
     * @var Font
     */
    protected Font $font;

    /**
     * @var int|null
     */
    protected ?int $maxW = null;

    /**
     * @var int|null
     */
    protected ?int $maxH = null;

    /**
     * @param Text ...$texts
     */
    public function __construct(protected string $orientation, protected int $gap = 5, Text ...$texts)
    {
        $this->orientation = in_array($orientation, ['h', 'v']) ? $orientation : 'v';
        $this->texts = $texts;
        $this->font = new Font('A', 30);
    }

    /**
     * @param Font $font
     * @return static
     */
    public function font(Font $font): static
    {
        $this->font = $font;
        $this->maxW = null;
        $this->maxH = null;
        return $this;
    }

    /**
     * @return array<Text>
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
    public function getGap(): int
    {
        return $this->gap;
    }

    /**
     * @return Font|null
     */
    public function getFont(): ?Font
    {
        return $this->font;
    }

    /**
     * @return int
     */
    public function getMaxW(): int
    {
        if (is_null($this->maxW)) {
            $this->maxW = 0;
            $textCount = count($this->texts);

            foreach ($this->texts as $text) {
                $font = $text->getFont() ?? $this->font;

                if ('v' === $this->orientation) {
                    $this->maxW = max($this->maxW, (int)ceil(Utils::estimateStringWidth($font, $text->getText())));
                } else {
                    $this->maxW += (int)ceil(Utils::estimateStringWidth($font, $text->getText()));
                }
            }

            if ('h' === $this->orientation && $textCount > 1) {
                $this->maxW += $this->gap * ($textCount - 1);
            }
        }

        return $this->maxW;
    }

    /**
     * @return int
     */
    public function getMaxH(): int
    {
        if (is_null($this->maxH)) {
            $this->maxH = 0;
            $textCount = count($this->texts);

            foreach ($this->texts as $text) {
                $font = $text->getFont() ?? $this->font;

                if ('v' === $this->orientation) {
                    $this->maxH += $font->getHeight();
                } else {
                    $this->maxH = max($this->maxH, $font->getHeight());
                }
            }

            if ('v' === $this->orientation && $textCount > 1) {
                $this->maxH += $this->gap * ($textCount - 1);
            }
        }

        return $this->maxH;
    }
}
