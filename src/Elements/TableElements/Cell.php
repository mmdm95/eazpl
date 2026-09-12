<?php

namespace Eazpl\Elements\TableElements;

use Eazpl\Contracts\RendererInterface;
use Eazpl\Elements\Font;
use Eazpl\Elements\HorizontalLine;
use Eazpl\Elements\Position;
use Eazpl\Elements\Text;
use Eazpl\Elements\TextBlock;
use Eazpl\Elements\VerticalLine;
use Eazpl\Elements\UnicodeText;
use Eazpl\Elements\Wrapper;
use Eazpl\Utils\RenderUtils;
use Eazpl\Utils\Utils;

class Cell implements RendererInterface
{
    /**
     * @var int
     */
    protected int $defaultHeight = 30;

    /**
     * @var Font
     */
    protected Font $defaultFont;

    /**
     * @var int
     */
    protected int $defaultBorderThickness = 3;

    /**
     * @var int
     */
    protected int $padding = 7;

    /**
     * @var int
     */
    protected int $renderedHeight = 0;

    /**
     * @var int
     */
    protected int $contentHeight = 0;

    /**
     * @param int $colIndex
     * @param int $x
     * @param int $y
     * @param string|array|RendererInterface $text
     * @param array{
     *      width: int,
     *      height: int,
     *      font: Font,
     *      border_bottom: bool,
     *      border_right: bool,
     *      border_thickness: int,
     *      padding: int,
     *  } $options
     */
    public function __construct(
        protected int                            $colIndex,
        protected int                            $x,
        protected int                            $y,
        protected string|array|RendererInterface $text,
        protected array                          $options = []
    )
    {
        $height = (int)$this->getOption('height', $this->defaultHeight);
        $this->defaultFont = new Font(
            'A',
            max(1, (int)round($height * 0.9))
        );
    }

    /**
     * @return int
     */
    public function getRenderedHeight(): int
    {
        return $this->renderedHeight;
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        $this->renderedHeight = 0;
        $this->contentHeight = 0;

        $borderThickness = (int)$this->getOption('border_thickness', $this->defaultBorderThickness);
        $cellWidth = (int)$this->getOption('width', 1);
        $padding = (int)$this->getOption('padding', $this->padding);

        $cellDefaultX = $this->x;
        $cellDefaultY = $this->y;

        $cellX = $cellDefaultX + $padding;
        $cellY = $cellDefaultY + $padding;

        $cellFont = $this->getCellFont($this->defaultFont);

        if (is_string($this->text)) {
            $zpl[] = $this->renderTextCell($this->text, $cellX, $cellY, $cellWidth, $cellFont);
        } elseif (is_array($this->text) && isset($this->text['text'])) {
            $font = $this->text['font'] ?? $cellFont;
            $zpl[] = $this->renderTextCell($this->text['text'], $cellX, $cellY, $cellWidth, $font);
        } elseif ($this->text instanceof RendererInterface) {
            $zpl[] = $this->renderRendererCell($this->text, $cellX, $cellY, $cellWidth, $cellFont);
        } else {
            return '';
        }

        $this->renderedHeight = ($this->contentHeight + ($padding * 2));

        if ($this->getOption('border_bottom', true)) {
            // Horizontal line
            $zpl[] =
                (new Position($cellDefaultX, $cellDefaultY + $this->renderedHeight,
                    new HorizontalLine($cellWidth, $borderThickness))
                )->render();
        }

        if ($this->getOption('border_right', false)) {
            // Vertical line
            $zpl[] =
                (new Position($cellDefaultX + $cellWidth, $cellDefaultY,
                    new VerticalLine($this->renderedHeight + $borderThickness, $borderThickness))
                )->render();
        }

        return implode('', $zpl);
    }

    /**
     * @param string $text
     * @param int $x
     * @param int $y
     * @param int $cellWidth
     * @param Font $font
     * @return string
     */
    protected function renderTextCell(string $text, int $x, int $y, int $cellWidth, Font $font): string
    {
        $wrappedLines = $this->wrapText(
            $text,
            $cellWidth,
            $this->getOption('padding', $this->padding),
            $font
        );

        $texts = [];
        $cellHeight = 0;
        foreach ($wrappedLines as $lineIndex => $line) {
            $fontHeight = $font->getHeight();
            $lineY = $y + ($lineIndex * $fontHeight);

            $text = Utils::isAscii($line)
                ? new Text($line, $font)
                : new UnicodeText($line, $font);

            $texts[] = (new Position($x, $lineY, $text));

            $cellHeight += $fontHeight;
        }

        $this->contentHeight = $cellHeight;

        return RenderUtils::renderInsiderElements($texts);
    }

    /**
     * @param RendererInterface $element
     * @param int $x
     * @param int $y
     * @param int $cellWidth
     * @param Font $font
     * @return string
     */
    protected function renderRendererCell(
        RendererInterface $element,
        int               $x,
        int               $y,
        int               $cellWidth,
        Font              $font
    ): string
    {
        $zpl = [];
        $contentY = 0;
        $padding = (int)$this->getOption('padding', $this->padding);

        if ($element instanceof TextBlock) {
            $this->contentHeight = $element->getFont()->getHeight();
            return (new Position($x, $y, $element))->render();
        }

        if ($element instanceof Wrapper) {
            foreach ($element->getElements() as $child) {
                $childY = $this->y + $padding + $contentY;

                if ($child instanceof Text) {
                    $zpl[] = $this->renderTextCell($child->getText(), $x, $childY, $cellWidth, $child->getFont() ?? $font);
                    $contentY += $this->contentHeight;
                    continue;
                }

                if ($child instanceof TextBlock) {
                    $zpl[] = (new Position($x, $childY, $child))->render();
                    $contentY += $child->getFont()->getHeight();
                    continue;
                }

                $zpl[] = (new Position($x, $childY, $child))->render();
                $contentY += (int)$this->getOption('height', $this->defaultHeight);
            }

            $this->contentHeight = $contentY;
            return implode('', $zpl);
        }

        $this->contentHeight = (int)$this->getOption('height', $this->defaultHeight);
        return (new Position($x, $y, $element))->render();
    }

    /**
     * @param string $text
     * @param int $cellWidth
     * @param int $padding
     * @param Font $font
     * @return array
     */
    protected function wrapText(string $text, int $cellWidth, int $padding, Font $font): array
    {
        $availableWidth = max(1, $cellWidth - ($padding * 2));
        $estimatedCharacterWidth = max(1, Utils::estimateStringWidth($font, 'x'));
        $charPerLine = max(1, (int)floor($availableWidth / $estimatedCharacterWidth));
        $text = str_replace('~BR', "\n", $text);

        return explode("\n", trim(Utils::utf8Wordwrap($text, $charPerLine, "\n", true)));
    }

    /**
     * @param Font $default
     * @return Font
     */
    protected function getCellFont(Font $default): Font
    {
        $font = $this->getOption('font', $default);
        return $font instanceof Font ? $font : $default;
    }

    /**
     * @param int $colIndex
     * @param string|null $key
     * @param mixed|null $default
     * @return mixed
     */
    public function getOption(?string $key = null, mixed $default = null): mixed
    {
        return $key ? ($this->options[$key] ?? $default) : $this->options;
    }
}
