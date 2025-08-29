<?php

namespace Eazpl\Elements;

use Eazpl\Contracts\RendererInterface;
use Eazpl\Utils\RenderUtils;
use InvalidArgumentException;

class Barcode implements RendererInterface
{
    /**
     * @var float
     */
    protected float $widthRatio = 2.0;

    /**
     * @param int $x
     * @param int $y
     * @param Text|string $code
     * @param int $height
     * @param int $width
     * @param BarcodeOption|null $options
     */
    public function __construct(
        protected int            $x,
        protected int            $y,
        protected Text|string    $code,
        protected int            $height,
        protected int            $width = 5,
        protected ?BarcodeOption $options = null
    )
    {
        $this->x = RenderUtils::getValidXYValue($this->x);
        $this->y = RenderUtils::getValidXYValue($this->y);

        if ($this->width < 1 || $this->width > 100) {
            throw new InvalidArgumentException('Height must be between 1 and 100');
        }
    }

    /**
     * @param float $widthRatio
     * @return static
     */
    public function widthRatio(float $widthRatio): static
    {
        if ($widthRatio < 2.0) {
            $widthRatio = 3.0;
        } elseif ($widthRatio > 3.0) {
            $widthRatio = 3.0;
        }

        $this->widthRatio = $widthRatio;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        $code = $this->code instanceof Text ? $this->code : new Text($this->code);
        $position = new Position($this->x, $this->y, new Raw('^BC' . ($this->options ? $this->options->render() : '')), $code);

        $widthRatio = ($this->widthRatio == floor($this->widthRatio) || number_format($this->widthRatio, 1) === number_format($this->widthRatio))
            ? (int)$this->widthRatio
            : number_format($this->widthRatio, 1);

        return sprintf(
            '^BY%d,%s,%d%s%s',
            $this->width,
            $widthRatio,
            $this->height,
            "\n",
            $position->render()
        );
    }
}
