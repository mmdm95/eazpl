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

                // Use the darkest of the three channels rather than a
                // luma-weighted average. Luma weighting (R*0.30 + G*0.59 +
                // B*0.11) is tuned for photographic brightness perception,
                // and it under-weights blue so heavily that mid-brightness
                // saturated colors (blues, purples, some reds) can compute
                // as "light" and vanish entirely even though they're
                // clearly visible ink against a white background. The
                // darkest channel is a much better proxy for "is this pixel
                // colored/inked at all" and correctly catches logos in any
                // hue, not just ones that happen to be dark overall.
                $darkest = $r < $g ? $r : $g;
                $darkest = $b < $darkest ? $b : $darkest;

                if ($darkest < 128) {
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
        // bin2hex() always produces lowercase hex, so this must match
        // case-insensitively or the trailing "all black" shortcut never fires.
        return preg_replace(['/0+$/', '/f+$/i'], [',', '!'], $row);
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
