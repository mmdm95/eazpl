<?php

namespace Eazpl\Elements;

use Eazpl\Contracts\InternalRendererInterface;
use Eazpl\Contracts\RendererInterface;
use InvalidArgumentException;

class Charset implements RendererInterface, InternalRendererInterface
{
    /**
     * @var array
     */
    protected array $customMappings = [];

    /**
     * @param int $charset
     * @param array ...$customMappings
     */
    public function __construct(protected int $charset = 28, array ...$customMappings)
    {
        if ($this->charset < 0 || $this->charset > 36) {
            throw new InvalidArgumentException('Charset must be between 0 and 36');
        }

        $this->customMappings = $customMappings;
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        return sprintf('^CI%d', $this->charset) .
            ($this->customMappings ? ',' . implode(',', $this->customMappings) : '');
    }
}
