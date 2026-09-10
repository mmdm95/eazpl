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
        string $string,
        int $width = 75,
        string $break = "\n",
        bool $cutLongWords = false
    ): string
    {
        $width = max(1, $width);
        $wrappedLines = [];

        foreach (preg_split('/\R/u', $string) as $line) {
            $words = preg_split('/\s+/u', $line, -1, PREG_SPLIT_NO_EMPTY);

            if (!$words) {
                $wrappedLines[] = '';
                continue;
            }

            $currentLine = '';

            foreach ($words as $word) {
                if ($currentLine === '') {
                    $candidate = $word;
                } else {
                    $candidate = $currentLine . ' ' . $word;
                }

                if (mb_strlen($candidate) <= $width || (!$cutLongWords && $currentLine !== '')) {
                    $currentLine = $candidate;
                    continue;
                }

                if ($currentLine !== '') {
                    $wrappedLines[] = $currentLine;
                    $currentLine = '';
                }

                if (!$cutLongWords || mb_strlen($word) <= $width) {
                    $currentLine = $word;
                    continue;
                }

                while (mb_strlen($word) > $width) {
                    $wrappedLines[] = mb_substr($word, 0, $width);
                    $word = mb_substr($word, $width);
                }

                $currentLine = $word;
            }

            $wrappedLines[] = $currentLine;
        }

        return implode($break, $wrappedLines);
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
