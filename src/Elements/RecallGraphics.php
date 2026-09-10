<?php

namespace Eazpl\Elements;

use Eazpl\Contracts\RendererInterface;
use Eazpl\Utils\RenderUtils;
use InvalidArgumentException;

class RecallGraphics implements RendererInterface
{
    public function __construct(
        protected string $path,
        protected int    $magnificationX = 1,
        protected int    $magnificationY = 1
    )
    {
        if (trim($this->path) === '') {
            throw new InvalidArgumentException('Graphics path is required.');
        }

        $this->magnificationX = RenderUtils::getValidValue($this->magnificationX, 'X magnification', 1, 10);
        $this->magnificationY = RenderUtils::getValidValue($this->magnificationY, 'Y magnification', 1, 10);
    }

    public function render(): string
    {
        return sprintf('^XG%s,%d,%d', $this->path, $this->magnificationX, $this->magnificationY);
    }
}
