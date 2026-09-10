<?php

namespace Eazpl\Elements;

use Eazpl\Contracts\RendererInterface;
use Eazpl\Enums\BoolEnums;
use Eazpl\Utils\RenderUtils;

class Code39 implements RendererInterface
{
    public function __construct(
        protected int                         $x,
        protected int                         $y,
        protected Text|string                 $data,
        protected int                         $height = 10,
        protected BoolEnums|string|bool|null  $includeLine = null,
        protected BoolEnums|string|bool|null  $lineAbove = null,
        protected BoolEnums|string|bool|null  $checkDigit = null
    )
    {
        $this->x = RenderUtils::getValidXYValue($this->x);
        $this->y = RenderUtils::getValidXYValue($this->y);
        $this->height = RenderUtils::getValidValue($this->height, 'Height', 1, 32_000);
        $this->includeLine = RenderUtils::getValidBoolean($this->includeLine);
        $this->lineAbove = RenderUtils::getValidBoolean($this->lineAbove);
        $this->checkDigit = RenderUtils::getValidBoolean($this->checkDigit);
    }

    public function render(): string
    {
        $data = $this->data instanceof Text ? $this->data : new Text($this->data);
        $parameters = '^B3N,' . ($this->checkDigit?->value ?? 'N') . ',' . $this->height;

        if ($this->includeLine !== null || $this->lineAbove !== null) {
            $parameters .= ',' . ($this->includeLine?->value ?? 'N');
        }

        if ($this->lineAbove !== null) {
            $parameters .= ',' . ($this->lineAbove?->value ?? 'N');
        }

        return (new Position($this->x, $this->y, new Raw($parameters), $data))->render();
    }
}
