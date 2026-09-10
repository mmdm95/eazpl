<?php

namespace Eazpl\Elements;

use Eazpl\Contracts\DecoderInterface;
use Eazpl\Contracts\RendererInterface;

class Image implements RendererInterface
{
    /**
     * @var int|null
     */
    protected ?int $width = null;

    /**
     * @var int|null
     */
    protected ?int $height = null;

    /**
     * @param DecoderInterface $decoder
     */
    public function __construct(protected DecoderInterface $decoder)
    {
    }

    /**
     * @param int $width
     * @return static
     */
    public function width(int $width): static
    {
        $this->width = $width;
        return $this;
    }

    /**
     * @param int $height
     * @return static
     */
    public function height(int $height): static
    {
        $this->height = $height;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        $decoder = $this->decoder;

        if ($this->width !== null || $this->height !== null) {
            $decoder = $decoder->resize($this->height ?? 0, $this->width);
        }

        $decoded = $decoder->decode();

        return sprintf(
            '^GFA,%d,%d,%d,%s',
            $decoded['totalBytes'],
            $decoded['totalBytes'],
            $decoded['rowBytes'],
            $decoded['data']
        );
    }
}
