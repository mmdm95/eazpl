<?php

namespace Eazpl\Elements;

use Eazpl\Contracts\RendererInterface;
use Eazpl\Utils\RenderUtils;
use InvalidArgumentException;

class PrintRate implements RendererInterface
{
    public function __construct(
        protected ?int $printSpeed = null,
        protected ?int $slewSpeed = null,
        protected ?int $backfeedSpeed = null
    )
    {
        RenderUtils::getValidValue($this->printSpeed ?? 1, 'Print speed', 1, 14);
        RenderUtils::getValidValue($this->slewSpeed ?? 1, 'Slew speed', 1, 14);
        RenderUtils::getValidValue($this->backfeedSpeed ?? 1, 'Backfeed speed', 1, 14);

        if ($this->printSpeed === null && $this->slewSpeed === null && $this->backfeedSpeed === null) {
            throw new InvalidArgumentException('At least one print rate is required.');
        }
    }

    public function render(): string
    {
        $speeds = [$this->printSpeed, $this->slewSpeed, $this->backfeedSpeed];
        $lastSpeedIndex = 0;

        foreach ($speeds as $index => $speed) {
            if ($speed !== null) {
                $lastSpeedIndex = $index;
            }
        }

        return '^PR' . implode(',', array_map(
            fn($speed) => $speed ?? 0,
            array_slice($speeds, 0, $lastSpeedIndex + 1)
        ));
    }
}
