<?php

namespace Eazpl\Contracts;

interface DecoderInterface
{
    /**
     * Decode the image file into ZPL-compatible hex.
     *
     * @return array{
     *   data: string,    // hex string
     *   totalBytes: int, // total byte count
     *   rowBytes: int,   // bytes per row
     *   rows: int        // number of rows
     * }
     */
    public function decode(): array;

    /**
     * @param int $height
     * @param int|null $width
     * @return static
     */
    public function resize(int $height, ?int $width = null): static;

    /**
     * Get the width of the image (in pixels).
     *
     * @return int
     */
    public function width(): int;

    /**
     * Get the height of the image (in pixels).
     *
     * @return int
     */
    public function height(): int;

    /**
     * Get the bit at the specified position.
     *
     * @param int $x
     * @param int $y
     * @return int
     */
    public function getBitAt(int $x, int $y): int;
}
