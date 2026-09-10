<?php

namespace Eazpl\Elements;

use Eazpl\Contracts\RendererInterface;
use Eazpl\Enums\AlignmentEnums;
use Eazpl\Utils\RenderUtils;

class FieldBlock implements RendererInterface
{
    /**
     * @var array<RendererInterface>
     */
    protected array $elements = [];

    /**
     * @param RendererInterface ...$elements
     */
    public function __construct(
        protected int                         $width,
        protected int                         $maxLines = 1,
        protected int                         $lineSpacing = 0,
        protected AlignmentEnums|int|null     $alignment = null,
        protected ?int                        $hangingIndent = null,
        RendererInterface                     ...$elements
    )
    {
        $this->width = RenderUtils::getValidValue($this->width, 'Width', 1, 32_000);
        $this->maxLines = RenderUtils::getValidValue($this->maxLines, 'Maximum lines', 1, 9_999);
        $this->lineSpacing = RenderUtils::getValidValue($this->lineSpacing, 'Line spacing', 0, 32_000);
        $this->alignment = is_null($this->alignment) ? null : RenderUtils::getValidAlignment($this->alignment);
        $this->hangingIndent = is_null($this->hangingIndent)
            ? null
            : RenderUtils::getValidValue($this->hangingIndent, 'Hanging indent', 0, 9_999);
        $this->elements = $elements;
    }

    public function render(): string
    {
        $values = [$this->width, $this->maxLines, $this->lineSpacing];

        if ($this->alignment !== null || $this->hangingIndent !== null) {
            $values[] = $this->alignment?->value ?? 0;
        }

        if ($this->hangingIndent !== null) {
            $values[] = $this->hangingIndent;
        }

        return '^FB' . implode(',', $values) .
            RenderUtils::renderInsiderElements($this->elements);
    }
}
