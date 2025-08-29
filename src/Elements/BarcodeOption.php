<?php

namespace Eazpl\Elements;

use Eazpl\Enums\BarcodeModeEnums;
use Eazpl\Enums\BoolEnums;
use Eazpl\Enums\FieldOrientationEnums;
use Eazpl\Utils\RenderUtils;

class BarcodeOption
{
    /**
     * @param FieldOrientationEnums|string|null $orientation
     * @param int|null $height
     * @param BoolEnums|string|bool|null $includeLine
     * @param BoolEnums|string|bool|null $lineAbove
     * @param BoolEnums|string|bool|null $checkDigit
     * @param BarcodeModeEnums|string|null $mode
     */
    public function __construct(
        protected FieldOrientationEnums|string|null $orientation = null,
        protected ?int                              $height = null,
        protected BoolEnums|string|bool|null        $includeLine = null,
        protected BoolEnums|string|bool|null        $lineAbove = null,
        protected BoolEnums|string|bool|null        $checkDigit = null,
        protected BarcodeModeEnums|string|null      $mode = null
    )
    {
        if (!is_null($this->height)) {
            $this->height = RenderUtils::getValidValue($this->height, 'Height', 1, 32_000);
        }

        $this->includeLine = RenderUtils::getValidBoolean($this->includeLine);
        $this->lineAbove = RenderUtils::getValidBoolean($this->lineAbove);
        $this->checkDigit = RenderUtils::getValidBoolean($this->checkDigit);
        $this->mode = RenderUtils::getValidBarcodeMode($this->mode);
    }

    /**
     * @param FieldOrientationEnums|string|null $orientation
     * @return static
     */
    public function setOrientation(FieldOrientationEnums|string|null $orientation): static
    {
        $this->orientation = $orientation;
        return $this;
    }

    /**
     * @param int|null $height
     * @return static
     */
    public function setHeight(?int $height): static
    {
        $this->height = $height;
        return $this;
    }

    /**
     * @param BoolEnums|string|bool|null $includeLine
     * @return static
     */
    public function includeLine(BoolEnums|string|bool|null $includeLine): static
    {
        $this->includeLine = $includeLine;
        return $this;
    }

    /**
     * @param BoolEnums|string|bool|null $lineAbove
     * @return static
     */
    public function lineAbove(BoolEnums|string|bool|null $lineAbove): static
    {
        $this->lineAbove = $lineAbove;
        return $this;
    }

    /**
     * @param BoolEnums|string|bool|null $checkDigit
     * @return static
     */
    public function checkDigit(BoolEnums|string|bool|null $checkDigit): static
    {
        $this->checkDigit = $checkDigit;
        return $this;
    }

    /**
     * @param BarcodeModeEnums|string|null $mode
     * @return static
     */
    public function setMode(BarcodeModeEnums|string|null $mode): static
    {
        $this->mode = $mode;
        return $this;
    }

    /**
     * @return string
     */
    public function render(): string
    {
        $orientation = $this->orientation;
        $height = $this->height;
        $includeLine = $this->includeLine;
        $lineAbove = $this->lineAbove;
        $checkDigit = $this->checkDigit;
        $mode = $this->mode;

        if (!is_null($this->height)) {
            $orientation = FieldOrientationEnums::_0;
        }

        if (!is_null($this->includeLine)) {
            $height = 10;
        }

        if (!is_null($this->lineAbove)) {
            $includeLine = BoolEnums::Y;
        }

        if (!is_null($this->checkDigit)) {
            $lineAbove = BoolEnums::Y;
        }

        if (!is_null($this->mode)) {
            $checkDigit = BoolEnums::N;
        }

        return ($orientation
            ? (
                $orientation->value . (
                !is_null($height)
                    ? ',' . $height . (
                    $includeLine
                        ? ',' . $includeLine->value . (
                        $lineAbove
                            ? ',' . $lineAbove->value . (
                            $checkDigit
                                ? ',' . $checkDigit->value . (
                                $mode ? ',' . $mode->value : ''
                                )
                                : ''
                            )
                            : ''
                        )
                        : ''
                    )
                    : ''
                )
            )
            : ''
        );
    }
}
