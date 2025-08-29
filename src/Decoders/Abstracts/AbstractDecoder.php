<?php

namespace Eazpl\Decoders\Abstracts;

use Eazpl\Contracts\DecoderInterface;

abstract class AbstractDecoder implements DecoderInterface
{
    /**
     * @inheritDoc
     */
    public function decode(): array
    {
        $width = $this->width();
        $height = $this->height();
        $rowBytes = (int)ceil($width / 8);
        $totalBytes = $rowBytes * $height;

        $compressedRows = [];
        $prevRow = null;

        for ($y = 0; $y < $height; $y++) {
            $rowBuffer = [];
            $byte = 0;
            $bitMask = 0x80; // start with 1000 0000

            for ($x = 0; $x < $width; $x++) {
                $rgb = $this->getBitAt($x, $y);

                $r = ($rgb >> 16) & 0xFF;
                $g = ($rgb >> 8) & 0xFF;
                $b = $rgb & 0xFF;

                // weighted grayscale (integer math, no division)
                $gray = ($r * 30) + ($g * 59) + ($b * 11);

                if ($gray < 12800) { // 128 * 100
                    $byte |= $bitMask;
                }

                $bitMask >>= 1;

                if ($bitMask === 0) {
                    // bin2hex(chr($byte)) is faster than sprintf('%02X', $byte)
                    $rowBuffer[] = bin2hex(chr($byte));
                    $byte = 0;
                    $bitMask = 0x80;
                }
            }

            // remaining bits
            if ($bitMask !== 0x80) {
                $rowBuffer[] = bin2hex(chr($byte));
            }

            $row = implode('', $rowBuffer);
            $compressedRows[] = $this->compressRow($row, $prevRow);
            $prevRow = $row;
        }

        return [
            'data'       => implode('', $compressedRows),
            'totalBytes' => $totalBytes,
            'rowBytes'   => $rowBytes,
            'rows'       => $height,
        ];
    }


    /**
     * @param string $row
     * @param string|null $preRow
     * @return string
     */
    protected function compressRow(string $row, ?string $preRow): string
    {
        if ($row === $preRow) {
            return ':';
        }

        $row = $this->compressTrailingZerosOrOnes($row);
        $row = $this->compressRepeatingCharacters($row);

        return $row;
    }

    /**
     * Replace trailing zeros or ones with a comma (,) or exclamation (!) respectively.
     *
     * @param string $row
     * @return string
     */
    protected function compressTrailingZerosOrOnes(string $row): string
    {
        return preg_replace(['/0+$/', '/F+$/'], [',', '!'], $row);
    }

    /**
     * Compress characters which repeat.
     *
     * @param string $row
     * @return string
     */
    protected function compressRepeatingCharacters(string $row): string
    {
        $callback = function ($matches) {
            $original = $matches[0];
            $repeat = strlen($original);
            $count = null;

            if ($repeat > 400) {
                $count .= str_repeat('z', floor($repeat / 400));
                $repeat %= 400;
            }

            if ($repeat > 19) {
                $count .= chr(ord('f') + floor($repeat / 20));
                $repeat %= 20;
            }

            if ($repeat > 0) {
                $count .= chr(ord('F') + $repeat);
            }

            return $count . substr($original, 1, 1);
        };

        return preg_replace_callback('/(.)(\1{2,})/', $callback, $row);
    }
}
