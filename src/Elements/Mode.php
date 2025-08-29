<?php

namespace Eazpl\Elements;

use Eazpl\Contracts\InternalRendererInterface;
use Eazpl\Contracts\RendererInterface;
use Eazpl\Enums\BoolEnums;
use Eazpl\Enums\ModeEnums;
use Eazpl\Utils\RenderUtils;

class Mode implements RendererInterface, InternalRendererInterface
{
    /**
     * @param ModeEnums $mode
     * @param BoolEnums|string|bool|null $prepeel
     */
    public function __construct(
        protected ModeEnums                  $mode = ModeEnums::T,
        protected BoolEnums|string|bool|null $prepeel = null
    )
    {
        $this->prepeel = RenderUtils::getValidBoolean($this->prepeel);
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        return sprintf('^MM%s', $this->mode->value) . ($this->prepeel ? ',' . $this->prepeel->value : '');
    }
}
