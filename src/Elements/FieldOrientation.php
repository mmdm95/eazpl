<?php

namespace Eazpl\Elements;

use Eazpl\Contracts\RendererInterface;
use Eazpl\Enums\AlignmentEnums;
use Eazpl\Enums\FieldOrientationEnums;
use Eazpl\Utils\RenderUtils;

class FieldOrientation implements RendererInterface
{
    /**
     * @param FieldOrientationEnums|string $orientation
     * @param AlignmentEnums|int|null $alignment
     */
    public function __construct(
        protected FieldOrientationEnums|string $orientation,
        protected AlignmentEnums|int|null      $alignment
    )
    {
        $this->orientation = RenderUtils::getValidFieldOrientation($this->orientation);

        if (!is_null($this->alignment)) {
            $this->alignment = RenderUtils::getValidAlignment($this->alignment);
        }
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        return
            sprintf('^FW%s', $this->orientation->value) .
            ($this->alignment ? ',' . $this->alignment->value : '');
    }
}
