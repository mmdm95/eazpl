<?php

namespace Eazpl\Elements\TableElements;

use Eazpl\Contracts\RendererInterface;
use Eazpl\Elements\Font;
use InvalidArgumentException;

class Row implements RendererInterface
{
    /**
     * @var int
     */
    protected int $defaultRowHeight = 30;

    /**
     * @var int
     */
    protected int $renderedHeight = 0;

    /**
     * @param int $rowIndex
     * @param int $x
     * @param int $y
     * @param int $width
     * @param array $cells
     * @param int|null $cellsCount
     * @param array<int, array<int, array{
     *      width: int,
     *      height: int,
     *      font: Font,
     *      border_bottom: bool,
     *      border_right: bool,
     *      border_thickness: int,
     *      padding: int,
     *  }>> $cellOptions
     * @param array<int, array{
     *      width: int,
     *      height: int,
     *      font: Font,
     *      border_bottom: bool,
     *      border_right: bool,
     *      border_thickness: int,
     *      padding: int,
     *  }> $rowOptions
     */
    public function __construct(
        protected int   $rowIndex,
        protected int   $x,
        protected int   $y,
        protected int   $width,
        protected array $cells,
        protected ?int  $cellsCount = null,
        protected array $cellOptions = [],
        protected array $rowOptions = [],
    )
    {
        if ($this->width < 1) {
            throw new InvalidArgumentException("Width must be greater than 0");
        }

        $this->cells = array_filter(
            $this->cells,
            fn($cell) => $cell instanceof RendererInterface || is_string($cell) || is_array($cell)
        );

        if (!count($this->cells)) {
            throw new InvalidArgumentException('Please provide at least one cell');
        }

        if ($this->cellsCount && count($cells) !== $this->cellsCount) {
            throw new InvalidArgumentException(
                sprintf("Row must have exactly %d columns.", $this->cellsCount)
            );
        }
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
        $zpl = [];

        $cellsCount = count($this->cells);
        $cellCounter = 0;

        $cellWidth = floor($this->width / $cellsCount);
        $maxHeight = PHP_INT_MIN;

        /**
         * @var int $colIndex
         * @var string|array|RendererInterface $cell
         */
        foreach ($this->cells as $colIndex => $cellScheme) {
            $cellCounter++;

            $rowHeight = $this->getCellOption($colIndex, 'height', $this->defaultRowHeight);
            $extraY = ($this->rowIndex * $rowHeight);

            $cellOptions = $this->getRenderingCellOptions($colIndex, $rowHeight, $cellCounter >= $cellsCount);

            if ($width = $this->getCellOption($colIndex, 'width', $cellWidth)) {
                $cellOptions['width'] = $width;
            }

            $cell = new Cell($colIndex, $this->x, $this->y + $extraY, $cellScheme, $cellOptions);

            $zpl[] = $cell->render();

            $maxHeight = max($maxHeight, $cell->getRenderedHeight());
        }

        $this->renderedHeight = $maxHeight;

        return implode('', $zpl);
    }

    /**
     * @param int $colIndex
     * @param int $rowHeight
     * @param bool $isLastCell
     * @return array<string, mixed>
     */
    protected function getRenderingCellOptions(int $colIndex, int $rowHeight, bool $isLastCell): array
    {
        $cellOptions = ['height' => $rowHeight];

        if ($cellPadding = $this->getCellOption($colIndex, 'padding', $this->getOption('padding'))) {
            $cellOptions['padding'] = $cellPadding;
        }

        if ($cellFont = $this->getCellOption($colIndex, 'font', $this->getOption('font'))) {
            $cellOptions['font'] = $cellFont;
        }

        $cellOptions['border_bottom'] = $this->getCellOption(
            $colIndex,
            'border_bottom',
            $this->getOption('border_bottom', true));

        $cellOptions['border_right'] = $this->getCellOption(
            $colIndex,
            'border_right',
            $this->getOption('border_right', false));

        if ($cellBorderThickness = $this->getCellOption(
            $colIndex,
            'border_thickness',
            $this->getOption('border_thickness')
        )) {
            $cellOptions['border_thickness'] = $cellBorderThickness;
        }

        if ($isLastCell) {
            $cellOptions['border_right'] = false;
        }

        return $cellOptions;
    }

    /**
     * @param int $colIndex
     * @param string|null $key
     * @param mixed|null $default
     * @return mixed
     */
    public function getCellOption(int $colIndex, ?string $key = null, mixed $default = null): mixed
    {
        return $key ? ($this->cellOptions[$colIndex][$key] ?? $default) : $this->cellOptions[$colIndex];
    }

    /**
     * @param string|null $key
     * @param mixed|null $default
     * @return mixed
     */
    protected function getOption(?string $key = null, mixed $default = null): mixed
    {
        return $key ? $this->rowOptions[$key] ?? $default : $this->rowOptions;
    }
}
