<?php

namespace Eazpl\Elements;

use Eazpl\Contracts\RendererInterface;
use InvalidArgumentException;

class FontDefinition implements RendererInterface
{
    public function __construct(protected string $fontName, protected string $path)
    {
        if (!preg_match('/^[0-9A-Z]$/', $this->fontName)) {
            throw new InvalidArgumentException('Font name must be 0-9 or A-Z');
        }

        if (trim($this->path) === '') {
            throw new InvalidArgumentException('Font path is required.');
        }
    }

    public function render(): string
    {
        return sprintf('^CW%s,%s', $this->fontName, $this->path);
    }
}
