<?php

namespace Eazpl\Elements;

use Eazpl\Contracts\RendererInterface;
use Eazpl\Elements\TableElements\Row;
use InvalidArgumentException;

class Table implements RendererInterface
{
    /**
     * @var array
     */
    protected array $rows = [];

    /**
     * @var int
     */
    protected int $defaultBorderThickness = 3;

    /**
     * @var int
     */
    protected int $renderedHeight = 0;

    /**
     * @param int $x
     * @param int $y
     * @param int $colsCount
     * @param int $tableWidth
     * @param array<int, array<int, string|array|RendererInterface>> $rows
     * @param array<int, array<int, array{
     *      width: int,
     *      height: int,
     *      font: Font,
     *      border_bottom: bool,
     *      border_right: bool,
     *      border_thickness: int,
     *      padding: int,
     *  }>> $cellsOptions
     * @param array<int, array{
     *      width: int,
     *      height: int,
     *      font: Font,
     *      border_bottom: bool,
     *      border_right: bool,
     *      border_thickness: int,
     *      padding: int,
     *  }> $rowsOptions
     * @param array{
     *      font: Font,
     *      border: bool,
     *      border_bottom: bool,
     *      border_right: bool,
     *      border_thickness: int,
     *      padding: int,
     *  } $tableOptions
     */
    public function __construct(
        protected int   $x,
        protected int   $y,
        protected int   $colsCount,
        protected int   $tableWidth,
        array           $rows = [],
        protected array $cellsOptions = [],
        protected array $rowsOptions = [],
        protected array $tableOptions = []
    )
    {
        if ($this->colsCount <= 0) {
            throw new InvalidArgumentException('Table must have at least 1 column.');
        }

        foreach ($rows as $row) {
            $this->addRow($row);
        }
    }

    /**
     * Add a row of cells.
     *
     * @param array<int, string|array|RendererInterface> $cells
     */
    public function addRow(array $cells): static
    {
        if (count($cells) !== $this->colsCount) {
            throw new InvalidArgumentException(
                sprintf("Row must have exactly %d columns.", $this->colsCount)
            );
        }

        $this->rows[] = $cells;
        return $this;
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

        $this->renderedHeight = 0;

        $rowsCount = count($this->rows);
        $rowCounter = 0;

        $borderThickness = $this->getOption('border_thickness', $this->defaultBorderThickness);

        foreach ($this->rows as $rowIndex => $cells) {
            $rowCounter++;

            $cellsOptions = $this->cellsOptions[$rowIndex] ?? [];
            $rowOptions = $this->getRenderingRowOption($rowIndex, $rowCounter >= $rowsCount);
            $borderThickness = $this->getRowOption($rowIndex, 'border_thickness', $borderThickness);

            $row = new Row(
                $rowIndex,
                $this->x,
                $this->y,
                $this->tableWidth,
                $cells,
                $this->colsCount,
                $cellsOptions,
                rowOptions: $rowOptions
            );

            $zpl[] = $row->render();

            $this->renderedHeight += $row->getRenderedHeight() + $borderThickness;
        }

        // Outer box
        array_unshift(
            $zpl,
            (new Position($this->x, $this->y,
                new Box($this->tableWidth, $this->renderedHeight, $borderThickness)
            ))->render()
        );

        return implode('', $zpl);
    }

    /**
     * @param int $rowIndex
     * @param bool $isLastRow
     * @return array
     */
    protected function getRenderingRowOption(int $rowIndex, bool $isLastRow): array
    {
        $rowOptions = [];

        if ($rowPadding = $this->getRowOption($rowIndex, 'padding', $this->getOption('padding'))) {
            $rowOptions['padding'] = $rowPadding;
        }

        if ($rowFont = $this->getRowOption($rowIndex, 'font', $this->getOption('font'))) {
            $rowOptions['font'] = $rowFont;
        }

        $rowOptions['border_bottom'] = $this->getRowOption(
            $rowIndex,
            'border_bottom',
            $this->getOption('border_bottom'));

        $rowOptions['border_right'] = $this->getRowOption(
            $rowIndex,
            'border_right',
            $this->getOption('border_right', true));

        if ($rowBorderThickness = $this->getRowOption(
            $rowIndex,
            'border_thickness',
            $this->getOption('border_thickness')
        )) {
            $rowOptions['border_thickness'] = $rowBorderThickness;
        }

        if ($isLastRow) {
            $rowOptions['border_bottom'] = false;
        }

        return $rowOptions;
    }

    /**
     * @param int $rowIndex
     * @param string|null $key
     * @param mixed|null $default
     * @return mixed
     */
    protected function getRowOption(int $rowIndex, ?string $key = null, mixed $default = null): mixed
    {
        return $key ? $this->rowsOptions[$rowIndex][$key] ?? $default : $this->rowsOptions[$rowIndex];
    }

    /**
     * @param string|null $key
     * @param mixed|null $default
     * @return mixed
     */
    protected function getOption(?string $key = null, mixed $default = null): mixed
    {
        return $key ? $this->tableOptions[$key] ?? $default : $this->tableOptions;
    }
}
