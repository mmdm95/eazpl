<?php

namespace Eazpl\Elements\TableElements;

use Eazpl\Contracts\RendererInterface;
use Eazpl\Elements\Font;
use Eazpl\Elements\HorizontalLine;
use Eazpl\Elements\Position;
use Eazpl\Elements\Text;
use Eazpl\Elements\TextBlock;
use Eazpl\Elements\VerticalLine;
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
        $width = $this->getOption('width');

        $this->defaultFont = new Font(
            'A',
            $this->getOption('height', $this->defaultHeight) * 0.9,
            $width ? $width - $this->getOption('padding', $this->padding) : null
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

        $borderThickness = $this->getOption('border_thickness', $this->defaultBorderThickness);
        $cellWidth = $this->getOption('width', 1);
        $padding = $this->getOption('padding', $this->padding);

        $cellDefaultX = $this->x + ($this->colIndex * $cellWidth);
        $cellDefaultY = $this->y;

        $cellX = $cellDefaultX + $padding;
        $cellY = $cellDefaultY + $padding;

        $cellFont = $this->getCellFont($this->defaultFont);

        if (is_string($this->text)) {
            $zpl[] = $this->renderTextCell($this->text, $cellX, $cellY, $cellWidth, $cellFont);
        } elseif (is_array($this->text) && isset($cell['text'])) {
            $font = $this->text['font'] ?? $cellFont;
            $zpl[] = $this->renderTextCell($this->text['text'], $cellX, $cellY, $cellWidth, $font);
        } elseif ($this->text instanceof RendererInterface) {
            $zpl[] = $this->renderRendererCell($this->text, $cellX, $cellY, $cellWidth, $cellFont);
        } else {
            return '';
        }

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
                (new Position($this->x + (($this->colIndex + 1) * $cellWidth), $cellDefaultY,
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
            $font->getWidth() ?? $font->getHeight()
        );

        $texts = [];
        $cellHeight = 0;
        foreach ($wrappedLines as $lineIndex => $line) {
            $fontHeight = $font->getHeight();
            $lineY = $y + ($lineIndex * $fontHeight);

            // TODO: Add support for unicode characters
//            if (!Utils::isAscii($line)) {
//                $texts = new UnicodeText($line, $font);
//            } else {
//                $text = new Text($line);
//            }

            $text = new Text($line, $font);

            $texts[] = (new Position($x, $lineY, $text));

            $cellHeight += $fontHeight;
        }

        $this->renderedHeight = $cellHeight;

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
        $els = [];

        if ($element instanceof TextBlock) {
            $this->renderedHeight = max(
                $this->renderedHeight,
                $element->getFont()->getRatio() * $element->getFont()->getHeight()
            );
        } elseif ($element instanceof Wrapper) {
            $cellY = $y;
            foreach ($element->getElements() as $el) {
                if ($el instanceof TextBlock) {
                    $this->renderedHeight = max(
                        $this->renderedHeight,
                        $el->getFont()->getRatio() * $el->getFont()->getHeight()
                    );
                } elseif ($el instanceof Text) {
                    $zpl[] = $this->renderTextCell($el->getText(), $x, $cellY, $cellWidth, $font);
                    $cellY += $this->renderedHeight;
                    continue;
                }

                $els[] = $el;
            }
        }

        return (new Position($x, $y, $element))->render() .
            RenderUtils::renderInsiderElements($els) .
            implode('', $zpl);
    }

    /**
     * @param string $text
     * @param int $cellWidth
     * @param int $fontWidth
     * @return array
     */
    protected function wrapText(string $text, int $cellWidth, int $fontWidth): array
    {
        $charPerLine = max(1, floor($cellWidth / $fontWidth));
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
