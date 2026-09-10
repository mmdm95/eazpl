<?php

namespace Eazpl\Elements;

use Eazpl\Contracts\RendererInterface;
use Eazpl\Enums\AlignmentEnums;
use Eazpl\Utils\RenderUtils;

class FieldTypeset implements RendererInterface
{
    /**
     * @var array<RendererInterface>
     */
    protected array $elements = [];

    /**
     * @param RendererInterface ...$elements
     */
    public function __construct(
        protected int                     $x,
        protected int                     $y,
        protected AlignmentEnums|int|null $alignment = null,
        RendererInterface                 ...$elements
    )
    {
        $this->x = RenderUtils::getValidXYValue($this->x);
        $this->y = RenderUtils::getValidXYValue($this->y);
        $this->alignment = is_null($this->alignment) ? null : RenderUtils::getValidAlignment($this->alignment);
        $this->elements = $elements;
    }

    public function render(): string
    {
        return sprintf('^FT%d,%d', $this->x, $this->y) .
            ($this->alignment ? ',' . $this->alignment->value : '') .
            RenderUtils::renderInsiderElements($this->elements) .
            '^FS' . "\n";
    }
}
