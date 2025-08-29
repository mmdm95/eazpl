<?php

namespace Eazpl\Utils;

use Eazpl\Elements\Font;

class Utils
{
    /**
     * url: https://en.wikipedia.org/wiki/Wikipedia:ASCII#ASCII_printable_characters
     *
     * @var string
     */
    private static $REGEX_ASCII = "[^\x09\x10\x13\x0A\x0D\x20-\x7E]";

    /**
     * @param $string
     * @param int $width
     * @param string $break
     * @param bool $cutLongWords
     * @return string
     *
     * @see https://www.php.net/manual/en/function.wordwrap.php#127205
     */
    public static function utf8Wordwrap(
        $string,
        int $width = 75,
        string $break = "\n",
        bool $cutLongWords = false
    ): string
    {
        if ($cutLongWords) {
            // Match anything 1 to $width chars long followed by whitespace or EOS,
            // otherwise match anything $width chars long
            $search = '/(.{1,' . $width . '})(?:\s|$)|(.{' . $width . '})/uS';
            $replace = '$1$2' . $break;
        } else {
            // Anchor the beginning of the pattern with a lookahead
            // to avoid crazy backtracking when words are longer than $width
            $search = '/(?=\s)(.{1,' . $width . '})(?:\s|$)/uS';
            $replace = '$1' . $break;
        }

        return preg_replace($search, $replace, $string);
    }

    /**
     * @param string $str
     * @return bool
     */
    public static function isAscii(string $str): bool
    {
        if ($str === '') {
            return true;
        }

        return !preg_match('/' . self::$REGEX_ASCII . '/', $str);
    }

    /**
     * @param Font $font
     * @param $string
     * @param float $charWidthRatio
     * @return float|int
     */
    public static function estimateStringWidth(Font $font, $string, float $charWidthRatio = 0.6): float|int
    {
        if ($font->getWidth()) {
            return $font->getWidth() * mb_strlen($string);
        }

        return $font->getHeight() * $charWidthRatio * mb_strlen($string);
    }
}