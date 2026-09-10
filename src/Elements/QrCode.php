<?php

namespace Eazpl\Elements;

use Eazpl\Contracts\RendererInterface;
use Eazpl\Enums\QrErrorCorrectionEnums;
use Eazpl\Utils\RenderUtils;

class QrCode implements RendererInterface
{
    public function __construct(
        protected int                         $x,
        protected int                         $y,
        protected Text|string                 $data,
        protected int                         $magnification = 2,
        protected QrErrorCorrectionEnums|string $errorCorrection = QrErrorCorrectionEnums::MEDIUM,
        protected ?int                        $mask = null
    )
    {
        $this->x = RenderUtils::getValidXYValue($this->x);
        $this->y = RenderUtils::getValidXYValue($this->y);
        $this->magnification = RenderUtils::getValidValue($this->magnification, 'Magnification', 1, 10);

        if (is_string($this->errorCorrection)) {
            $this->errorCorrection = QrErrorCorrectionEnums::tryFrom($this->errorCorrection) ??
                throw new \InvalidArgumentException('Invalid QR error correction.');
        }

        if ($this->mask !== null) {
            $this->mask = RenderUtils::getValidValue($this->mask, 'Mask', 0, 7);
        }
    }

    public function render(): string
    {
        $data = $this->data instanceof Text ? $this->data : new Text($this->data);
        $parameters = sprintf(
            '^BQN,2,%d,%s',
            $this->magnification,
            $this->errorCorrection->value
        );

        if ($this->mask !== null) {
            $parameters .= ',' . $this->mask;
        }

        return (new Position($this->x, $this->y, new Raw($parameters), $data))->render();
    }
}
