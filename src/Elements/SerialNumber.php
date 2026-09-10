<?php

namespace Eazpl\Elements;

use Eazpl\Contracts\RendererInterface;
use Eazpl\Enums\BoolEnums;
use Eazpl\Utils\RenderUtils;
use InvalidArgumentException;

class SerialNumber implements RendererInterface
{
    public function __construct(
        protected string                    $start = '1',
        protected int|string                $increment = 1,
        protected BoolEnums|string|bool|null $pad = null
    )
    {
        if (is_string($this->increment) && !is_numeric($this->increment)) {
            throw new InvalidArgumentException('Increment must be numeric.');
        }

        $increment = (int)$this->increment;
        if ($increment < -9_999 || $increment > 9_999) {
            throw new InvalidArgumentException('Increment must be between -9999 and 9999.');
        }
        $this->increment = $increment;

        $this->pad = RenderUtils::getValidBoolean($this->pad);
    }

    public function render(): string
    {
        return sprintf('^SN%s,%d', $this->start, $this->increment) .
            ($this->pad ? ',' . $this->pad->value : '');
    }
}
