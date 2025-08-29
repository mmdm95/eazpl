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
        } elseif ($image instanceof GdImage) {
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
        return static::fromString(file_get_contents($path));
    }

    /**
     * Create a new decoder instance from the specified string.
     *
     * @param string $data
     * @return static
     */
    public static function fromString(string $data): static
    {
        if (false === $image = imagecreatefromstring($data)) {
            throw new InvalidArgumentException('Could not read image');
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

        if (!$width) {
            $width = (int)(($height / $originalHeight) * $originalWidth);
        } elseif(!$height) {
            $height = (int)(($width / $originalWidth) * $originalHeight);
        }

        // Create a new true color image
        $resizedImage = imagecreatetruecolor($width, $height);

        // Preserve alpha
        imagesavealpha($resizedImage, true);
        imagealphablending($resizedImage, false);

        // Resize the original image and copy it to the new image
        imagecopyresampled(
            $resizedImage,
            $this->image,
            0,
            0,
            0,
            0,
            $width,
            $height,
            $originalWidth,
            $originalHeight
        );

        $this->image = $resizedImage;

        imagedestroy($resizedImage);

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
