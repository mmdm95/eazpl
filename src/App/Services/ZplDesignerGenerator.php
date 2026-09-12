<?php

namespace Eazpl\App\Services;

use Eazpl\ZplPrinter;

final class ZplDesignerGenerator
{
    public function __construct(private readonly ZplComponentRegistry $registry)
    {
    }

    public function generate(array $state): string
    {
        $printer = new ZplPrinter();

        foreach ($state['components'] ?? [] as $instance) {
            $printer->addElement($this->registry->create($instance));
        }

        return $printer->build();
    }
}
