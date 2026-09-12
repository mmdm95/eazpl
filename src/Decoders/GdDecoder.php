<?php

namespace Eazpl\Decoders;

use Eazpl\Decoders\Abstracts\AbstractDecoder;
use GdImage;
use InvalidArgumentException;

class GdDecoder extends AbstractDecoder
{
    /**
     * The GD image resource.
     *
     * @var resource|GdImage
     */
    protected $image;

    /**
     * Background color used for transparency blending.
     * Default: white (0xFFFFFFFF).
     *
     * @var int
     */
    protected int $backgroundColor = 0xFFFFFFFF;

    /**
     * Create a new decoder instance.
     *
     * @param resource|GdImage $image
     */
    public function __construct($image)
    {
        if (!$this->isGdResource($image)) {
            throw new InvalidArgumentException('Invalid resource');
        }

        if (!imageistruecolor($image)) {
            imagepalettetotruecolor($image);
        }

        // Preserve alpha
        imagesavealpha($image, true);
        imagealphablending($image, false);

        $this->image = $image;
    }

    /**
     * @param $image
     * @return bool
     */
    protected function isGdResource($image): bool
    {
        if (is_resource($image)) {
            return get_resource_type($image) === 'gd';
        }

        if ($image instanceof GdImage) {
            return true;
        }

        return false;
    }

    /**
     * Destroy the instance.
     */
    public function __destruct()
    {
        imagedestroy($this->image);
    }

    /**
     * Create a new decoder instance from the specified file path.
     *
     * @param string $path
     * @return static
     */
    public static function fromPath(string $path): static
    {
        if (!is_file($path) || !is_readable($path)) {
            throw new InvalidArgumentException("File not found or not readable: {$path}");
        }

        $data = file_get_contents($path);

        if ($data === false) {
            throw new InvalidArgumentException("Could not read file: {$path}");
        }

        return static::fromString($data);
    }

    /**
     * Create a new decoder instance from the specified string.
     *
     * GD and libpng can emit warnings for non-fatal metadata issues, such as an
     * incorrectly formatted PNG iCCP/sRGB profile. Those warnings do not affect
     * decoding, so they are collected here instead of leaking into API output.
     *
     * @param string $data
     * @return static
     */
    public static function fromString(string $data): static
    {
        if ($data === '') {
            throw new InvalidArgumentException('Could not read image: empty data');
        }

        $warnings = [];
        set_error_handler(static function (
            int $severity,
            string $message
        ) use (&$warnings): bool {
            $warnings[] = $message;

            return true;
        });

        try {
            $image = imagecreatefromstring($data);
        } finally {
            restore_error_handler();
        }

        if ($image === false) {
            throw new InvalidArgumentException(
                'Could not read image' . ($warnings === [] ? '' : ': ' . implode('; ', $warnings)),
            );
        }

        return new static($image);
    }

    /**
     * @inheritDoc
     */
    public function resize(?int $height = null, ?int $width = null): static
    {
        if (
            (!$height && !$width) ||
            ($height && $height <= 0) ||
            ($width && $width <= 0)
        ) {
            return $this;
        }

        $originalWidth = $this->width();
        $originalHeight = $this->height();

        if ($originalWidth <= 0 || $originalHeight <= 0) {
            return $this;
        }

        if ($width && $height) {
            // Both dimensions given: fit the image WITHIN the box rather
            // than stretching it to fill it exactly. Stretching to an
            // arbitrary width x height ignores the original aspect ratio
            // and distorts the shape (e.g. a wide logo squashed into a
            // square). Instead we scale by whichever dimension is more
            // constraining and center the result on a canvas of exactly
            // the requested size, padding the rest with transparency.
            $scale = min($width / $originalWidth, $height / $originalHeight);
            $scaledWidth = max(1, (int)round($originalWidth * $scale));
            $scaledHeight = max(1, (int)round($originalHeight * $scale));
            $canvasWidth = $width;
            $canvasHeight = $height;
        } elseif (!$width) {
            $scaledHeight = $height;
            $scaledWidth = max(1, (int)round(($height / $originalHeight) * $originalWidth));
            $canvasWidth = $scaledWidth;
            $canvasHeight = $scaledHeight;
        } else { // !$height
            $scaledWidth = $width;
            $scaledHeight = max(1, (int)round(($width / $originalWidth) * $originalHeight));
            $canvasWidth = $scaledWidth;
            $canvasHeight = $scaledHeight;
        }

        // Scale the original image onto an intermediate canvas at the
        // aspect-ratio-correct size.
        $scaledImage = imagecreatetruecolor($scaledWidth, $scaledHeight);
        imagesavealpha($scaledImage, true);
        imagealphablending($scaledImage, false);
        imagefilledrectangle(
            $scaledImage,
            0,
            0,
            $scaledWidth,
            $scaledHeight,
            imagecolorallocatealpha($scaledImage, 0, 0, 0, 127)
        );

        imagecopyresampled(
            $scaledImage,
            $this->image,
            0,
            0,
            0,
            0,
            $scaledWidth,
            $scaledHeight,
            $originalWidth,
            $originalHeight
        );

        if ($canvasWidth === $scaledWidth && $canvasHeight === $scaledHeight) {
            $finalImage = $scaledImage;
        } else {
            // Center the scaled image on the requested canvas, padding
            // the surrounding area with transparency (never stretching).
            $finalImage = imagecreatetruecolor($canvasWidth, $canvasHeight);
            imagesavealpha($finalImage, true);
            imagealphablending($finalImage, false);
            imagefilledrectangle(
                $finalImage,
                0,
                0,
                $canvasWidth,
                $canvasHeight,
                imagecolorallocatealpha($finalImage, 0, 0, 0, 127)
            );

            $offsetX = (int)floor(($canvasWidth - $scaledWidth) / 2);
            $offsetY = (int)floor(($canvasHeight - $scaledHeight) / 2);

            imagecopy($finalImage, $scaledImage, $offsetX, $offsetY, 0, 0, $scaledWidth, $scaledHeight);
            imagedestroy($scaledImage);
        }

        // Free the ORIGINAL image now that we've copied from it.
        // (Previously this destroyed $resizedImage itself, since $this->image
        // and $resizedImage reference the same underlying GD resource once
        // assigned — that left $this->image pointing at a destroyed image,
        // so any decode() called after resize() would fail or read garbage.)
        $oldImage = $this->image;
        $this->image = $finalImage;
        imagedestroy($oldImage);

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function width(): int
    {
        return imagesx($this->image);
    }

    /**
     * @inheritDoc
     */
    public function height(): int
    {
        return imagesy($this->image);
    }

    /**
     * @inheritDoc
     */
    public function getBitAt(int $x, int $y): int
    {
        $rgba = imagecolorat($this->image, $x, $y);

        // Extract channels
        $a = ($rgba & 0x7F000000) >> 24; // 0–127 (GD alpha)
        $r = ($rgba >> 16) & 0xFF;
        $g = ($rgba >> 8) & 0xFF;
        $b = $rgba & 0xFF;

        // Convert to 0–255 alpha (255 opaque, 0 transparent)
        $alpha = (int)round((127 - $a) * 255 / 127);

        if ($alpha < 255) {
            // Blend with background
            $bgA = ($this->backgroundColor >> 24) & 0xFF;
            $bgR = ($this->backgroundColor >> 16) & 0xFF;
            $bgG = ($this->backgroundColor >> 8) & 0xFF;
            $bgB = $this->backgroundColor & 0xFF;

            $blendFactor = $alpha / 255;

            $r = (int)round($r * $blendFactor + $bgR * (1 - $blendFactor));
            $g = (int)round($g * $blendFactor + $bgG * (1 - $blendFactor));
            $b = (int)round($b * $blendFactor + $bgB * (1 - $blendFactor));
            $alpha = 255; // blended result is fully opaque
        }

        return ($alpha << 24) | ($r << 16) | ($g << 8) | $b;
    }
}
