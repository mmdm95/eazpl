<?php

namespace Eazpl\Elements;

use Eazpl\Contracts\RendererInterface;
use Eazpl\Enums\BoolEnums;
use Eazpl\Utils\RenderUtils;
use InvalidArgumentException;

class PrintQuantity implements RendererInterface
{
    public function __construct(
        protected int                        $labels = 1,
        protected ?int                       $labelsBetweenPauses = null,
        protected ?int                       $replicates = null,
        protected BoolEnums|string|bool|null $noPause = null,
        protected BoolEnums|string|bool|null $cutOnError = null
    )
    {
        $this->labels = RenderUtils::getValidValue($this->labels, 'Labels', 1);
        $this->labelsBetweenPauses = RenderUtils::getValidValue(
            $this->labelsBetweenPauses ?? 0,
            'Labels between pauses',
            0
        ) ?: null;
        $this->replicates = RenderUtils::getValidValue($this->replicates ?? 0, 'Replicates', 0) ?: null;
        $this->noPause = RenderUtils::getValidBoolean($this->noPause);
        $this->cutOnError = RenderUtils::getValidBoolean($this->cutOnError);

        if ($this->labelsBetweenPauses !== null && $this->labelsBetweenPauses > $this->labels) {
            throw new InvalidArgumentException('Labels between pauses must not exceed the label quantity.');
        }
    }

    public function render(): string
    {
        $values = [$this->labels];

        foreach ([$this->labelsBetweenPauses, $this->replicates] as $value) {
            if ($value !== null) {
                $values[] = $value;
            }
        }

        foreach ([$this->noPause, $this->cutOnError] as $value) {
            if ($value !== null) {
                while (count($values) < 3) {
                    $values[] = 0;
                }
                $values[] = $value->value;
            }
        }

        return '^PQ' . implode(',', $values);
    }
}
