<?php

namespace Eazpl\Elements;

use Eazpl\Contracts\RendererInterface;
use InvalidArgumentException;

class RecallFormat implements RendererInterface
{
    public function __construct(protected string $path)
    {
        if (trim($this->path) === '') {
            throw new InvalidArgumentException('Format path is required.');
        }
    }

    public function render(): string
    {
        return sprintf('^XF%s', $this->path);
    }
}
