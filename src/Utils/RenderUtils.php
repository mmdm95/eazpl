<?php

namespace Eazpl\Utils;

use BackedEnum;
use Eazpl\Contracts\InternalRendererInterface;
use Eazpl\Contracts\RendererInterface;
use Eazpl\Enums\AlignmentEnums;
use Eazpl\Enums\BarcodeModeEnums;
use Eazpl\Enums\BoolEnums;
use Eazpl\Enums\ColorEnums;
use Eazpl\Enums\FieldOrientationEnums;
use Eazpl\Enums\LineOrientationEnums;
use InvalidArgumentException;
use TypeError;

class RenderUtils
{
    /**
     * @param array $elements
     * @param string $separator
     * @return string
     */
    public static function renderInsiderElements(array $elements, string $separator = ''): string
    {
        $elements = array_filter(
            $elements,
            fn($element) => ($element instanceof RendererInterface) && !($element instanceof InternalRendererInterface)
        );
        return implode($separator, array_map(fn($element) => $element->render(), $elements));
    }

    /**
     * @param int $xy
     * @return int
     */
    public static function getValidXYValue(int $xy): int
    {
        if ($xy < 0 || $xy > 32_000) {
            throw new InvalidArgumentException("Position value must be between 0 and 32000");
        }

        return $xy;
    }

    /**
     * @param BackedEnum|string|int|null $enum
     * @param string $enumType
     * @param string $enumName
     * @param bool $shouldCheckNull
     * @return BackedEnum|null
     */
    public static function getValidEnumOf(
        BackedEnum|string|int|null $enum,
        string                     $enumType,
        string                     $enumName,
        bool                       $shouldCheckNull = false
    ): ?BackedEnum
    {
        $isValidEnumType = !enum_exists($enumType) || !method_exists($enumType, 'tryFrom');

        if ($shouldCheckNull && (!$isValidEnumType || !$enum)) {
            return null;
        }

        if ($enum instanceof $enumType) {
            return $enum;
        }

        if (!$isValidEnumType) {
            throw new InvalidArgumentException("Invalid enum [$enumType] provided.");
        }

        $throwMessage = "$enumName must be one of [" .
            implode(', ', array_map(fn($item) => $item->value, $enumType::cases())) .
            ']';

        try {
            if (is_null($newEnum = $enumType::tryFrom($enum))) {
                throw new InvalidArgumentException($throwMessage);
            }
        } catch (TypeError) {
            throw new InvalidArgumentException($throwMessage);
        }

        return $newEnum;
    }

    /**
     * @param FieldOrientationEnums|string $orientation
     * @return FieldOrientationEnums
     */
    public static function getValidFieldOrientation(FieldOrientationEnums|string $orientation): FieldOrientationEnums
    {
        /** @var FieldOrientationEnums $enum */
        $enum = static::getValidEnumOf($orientation, FieldOrientationEnums::class, 'Orientation');
        return $enum;
    }

    /**
     * @param AlignmentEnums|int $alignment
     * @return AlignmentEnums
     */
    public static function getValidAlignment(AlignmentEnums|int $alignment): AlignmentEnums
    {
        /** @var AlignmentEnums $enum */
        $enum = static::getValidEnumOf($alignment, AlignmentEnums::class, 'Alignment');
        return $enum;
    }

    /**
     * @param ColorEnums|string|null $color
     * @return ColorEnums|null
     */
    public static function getValidColor(ColorEnums|string|null $color): ?ColorEnums
    {
        /** @var ColorEnums|null $enum */
        $enum = static::getValidEnumOf($color, ColorEnums::class, 'Color', true);
        return $enum;
    }

    /**
     * @param LineOrientationEnums|string|null $orientation
     * @return LineOrientationEnums|null
     */
    public static function getValidLineOrientation(LineOrientationEnums|string|null $orientation): ?LineOrientationEnums
    {
        /** @var LineOrientationEnums|null $enum */
        $enum = static::getValidEnumOf($orientation, LineOrientationEnums::class, 'Orientation', true);
        return $enum;
    }

    /**
     * @param BoolEnums|string|bool|null $bool
     * @return BoolEnums|null
     */
    public static function getValidBoolean(BoolEnums|string|bool|null $bool): ?BoolEnums
    {
        if (!$bool) {
            return null;
        }

        if ($bool instanceof BoolEnums) {
            return $bool;
        }

        if (in_array($bool, [false, true], true)) {
            return $bool ? BoolEnums::Y : BoolEnums::N;
        }

        if (is_null($newBool = BoolEnums::tryFrom($bool))) {
            throw new InvalidArgumentException(
                'Boolean value must be one of [' .
                implode(', ', array_map(fn($item) => $item->value, BoolEnums::cases())) .
                ']'
            );
        }

        return $newBool;
    }

    /**
     * @param BarcodeModeEnums|string|null $mode
     * @return BarcodeModeEnums|null
     */
    public static function getValidBarcodeMode(BarcodeModeEnums|string|null $mode): ?BarcodeModeEnums
    {
        /** @var BarcodeModeEnums|null $enum */
        $enum = static::getValidEnumOf($mode, BarcodeModeEnums::class, 'Barcode mode', true);
        return $enum;
    }

    /**
     * @param int $value
     * @param string $valueName
     * @param int|null $min
     * @param int|null $max
     * @return int
     */
    public static function getValidValue(int $value, string $valueName, ?int $min = null, ?int $max = null): int
    {
        $hasMin = is_null($min);
        $hasMax = is_null($max);

        if ($hasMin && $hasMax && $value < $min && $value > $max) {
            throw new InvalidArgumentException("$valueName must be between $min and $max");
        } elseif ($hasMax && $value > $max) {
            throw new InvalidArgumentException("$valueName must be less than or equal to $max");
        } elseif ($hasMin && $value < $min) {
            throw new InvalidArgumentException("$valueName must be greater than or equal to $min");
        }

        return $value;
    }
}
