<?php

namespace Eazpl\Elements;

use Eazpl\Contracts\RendererInterface;
use Eazpl\Decoders\GdDecoder;

class UnicodeText implements RendererInterface
{
    /**
     * @param string $text
     * @param Font $font
     */
    public function __construct(protected string $text, protected Font $font)
    {
    }

    /**
     * @inheritDoc
     */
    public function render(): string
    {
        $image = $this->getTextImage($this->text, $this->font);
        return (new Image(new GdDecoder($image)))->render();
    }

    /**
     * @param string $text
     * @param Font $font
     * @return mixed
     */
    protected function getTextImage(string $text, Font $font): mixed
    {
        $fontHeight = $font->getHeight();
        $fontPath = $font->getFontFace();
        $fontSize = $fontHeight;
        $angle = 0;

        // Get text bounding box
        $bbox = imagettfbbox($fontSize, $angle, $fontPath, $text);

        $textWidth = abs($bbox[2] - $bbox[0]);
        $textHeight = abs($bbox[7] - $bbox[1]);

        // Add padding
        $width = $textWidth + 10;
        $height = $textHeight + 10;

        // Create image
        $image = imagecreatetruecolor($width, $height);

        // Colors
        $backgroundColor = imagecolorallocate($image, 255, 255, 255);
        $textColor = imagecolorallocate($image, 0, 0, 0);

        // Fill background
        imagefill($image, 0, 0, $backgroundColor);

        // Proper baseline for text
        $x = 5;
        // baseline = height - descent (bbox[1] is usually negative)
        $y = $height - 5;

        // Draw text
        imagettftext($image, $fontSize, $angle, $x, $y, $textColor, $fontPath, $text);

        return $image;
    }
}
