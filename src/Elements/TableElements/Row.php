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

        if ($this->cellsCount && count($this->cells) !== $this->cellsCount) {
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
        $maxHeight = PHP_INT_MIN;
        $cellWidths = $this->getCellWidths();
        $cursorX = $this->x;
        $cellCounter = 0;

        /**
         * @var int $colIndex
         * @var string|array|RendererInterface $cell
         */
        foreach ($this->cells as $colIndex => $cellScheme) {
            $cellCounter++;

            $rowHeight = (int)$this->getCellOption($colIndex, 'height', $this->defaultRowHeight);
            $cellWidth = $cellWidths[$colIndex];

            $cellOptions = $this->getRenderingCellOptions($colIndex, $rowHeight, $cellCounter >= $cellsCount);

            $cellOptions['width'] = $cellWidth;

            $cell = new Cell($colIndex, $cursorX, $this->y, $cellScheme, $cellOptions);

            $zpl[] = $cell->render();

            $maxHeight = max($maxHeight, $cell->getRenderedHeight());
            $cursorX += $cellWidth;
        }

        $this->renderedHeight = $maxHeight;

        return implode('', $zpl);
    }

    /**
     * @return array<int, int>
     */
    protected function getCellWidths(): array
    {
        $widths = [];
        $explicitWidth = 0;
        $columnsWithoutWidth = [];

        foreach (array_keys($this->cells) as $colIndex) {
            $width = $this->getCellOption($colIndex, 'width');

            if (is_numeric($width) && (int)$width > 0) {
                $widths[$colIndex] = (int)$width;
                $explicitWidth += (int)$width;
                continue;
            }

            $columnsWithoutWidth[] = $colIndex;
        }

        if ($explicitWidth > $this->width) {
            throw new InvalidArgumentException('The sum of cell widths must not exceed the table width.');
        }

        if (count($columnsWithoutWidth) === 0) {
            return $widths;
        }

        $remainingWidth = $this->width - $explicitWidth;
        $remainingColumns = count($columnsWithoutWidth);
        $defaultWidth = (int)floor($remainingWidth / $remainingColumns);
        $lastColumn = array_key_last($columnsWithoutWidth);

        foreach ($columnsWithoutWidth as $colIndex) {
            $widths[$colIndex] = $colIndex === $lastColumn
                ? $remainingWidth - ($defaultWidth * ($remainingColumns - 1))
                : $defaultWidth;
        }

        return $widths;
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
