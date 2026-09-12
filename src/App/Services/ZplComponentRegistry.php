<?php

namespace Eazpl\App\Services;

use Eazpl\Decoders;
use Eazpl\Elements;
use Eazpl\Contracts\RendererInterface;
use Eazpl\Enums\ColorEnums;
use InvalidArgumentException;

final class ZplComponentRegistry
{
    /** @var array<string, ZplComponentDefinition> */
    private array $definitions = [];

    public function __construct()
    {
        $this->registerDefaults();
    }

    public function register(ZplComponentDefinition $definition): static
    {
        $this->definitions[$definition->type] = $definition;

        return $this;
    }

    public function get(string $type): ZplComponentDefinition
    {
        return $this->definitions[$type] ?? throw new InvalidArgumentException(
            "Unsupported ZPL component type: {$type}.",
        );
    }

    /** @return list<ZplComponentDefinition> */
    public function all(): array
    {
        return array_values($this->definitions);
    }

    public function create(array $instance): RendererInterface
    {
        return $this->get($instance['type'])->create($instance);
    }

    private function registerDefaults(): void
    {
        $this->register(new ZplComponentDefinition(
            'text',
            'Text',
            'type',
            'A positioned text field with a built-in printer font.',
            'Content',
            'text',
            [
                new ZplAttributeDefinition('text', 'text', 'Text', true, 'Text'),
                new ZplAttributeDefinition('fontName', 'select', 'Font', true, '0', self::fontOptions()),
            ],
            self::textFactory(),
            140,
            30,
        ));

        $this->register(new ZplComponentDefinition(
            'barcode',
            'Code 128 Barcode',
            'barcode',
            'A Code 128 barcode with configurable modules and orientation.',
            'Codes',
            'code',
            [
                new ZplAttributeDefinition('value', 'text', 'Value', true, '123456789'),
                new ZplAttributeDefinition('moduleWidth', 'number', 'Module width', true, 3),
                new ZplAttributeDefinition('widthRatio', 'number', 'Width ratio', true, 2.0),
                new ZplAttributeDefinition('includeLine', 'boolean', 'Print interpretation line', false, false),
                new ZplAttributeDefinition('lineAbove', 'boolean', 'Interpretation line above', false, false),
                new ZplAttributeDefinition('checkDigit', 'boolean', 'Check digit', false, false),
                new ZplAttributeDefinition('mode', 'select', 'Mode', false, 'N', [
                    ['label' => 'No mode', 'value' => 'N'],
                    ['label' => 'UCC case mode', 'value' => 'U'],
                    ['label' => 'Automatic mode', 'value' => 'A'],
                    ['label' => 'UCC/EAN mode', 'value' => 'D'],
                ]),
            ],
            self::barcodeFactory(),
            180,
            90,
        ));

        $this->register(new ZplComponentDefinition(
            'qr-code',
            'QR Code',
            'qr-code',
            'A QR code suitable for URLs and compact data.',
            'Codes',
            'code',
            [
                new ZplAttributeDefinition('data', 'text', 'Data', true, 'https://example.com'),
                new ZplAttributeDefinition('magnification', 'number', 'Magnification', true, 3),
                new ZplAttributeDefinition('errorCorrection', 'select', 'Error correction', true, 'M', [
                    ['label' => 'Low', 'value' => 'L'],
                    ['label' => 'Medium', 'value' => 'M'],
                    ['label' => 'Quartile', 'value' => 'Q'],
                    ['label' => 'High', 'value' => 'H'],
                ]),
            ],
            self::qrCodeFactory(),
            90,
            90,
            true,
            false,
        ));

        $this->register(new ZplComponentDefinition(
            'data-matrix',
            'Data Matrix',
            'data-matrix',
            'A compact Data Matrix code.',
            'Codes',
            'code',
            [
                new ZplAttributeDefinition('data', 'text', 'Data', true, 'EAZPL'),
            ],
            self::dataMatrixFactory(),
            80,
            80,
            true,
            false,
        ));

        $this->register(new ZplComponentDefinition(
            'box',
            'Box',
            'square',
            'A rectangular outline or filled block.',
            'Shapes',
            'shape',
            [
                new ZplAttributeDefinition('thickness', 'number', 'Thickness', true, 3),
                new ZplAttributeDefinition('color', 'select', 'Color', true, 'B', [
                    ['label' => 'Black', 'value' => 'B'],
                    ['label' => 'White', 'value' => 'W'],
                ]),
                new ZplAttributeDefinition('rounding', 'number', 'Corner rounding', false, 0),
            ],
            self::boxFactory(),
            120,
            80,
            true,
            false,
        ));

        $this->register(new ZplComponentDefinition(
            'circle',
            'Circle',
            'circle',
            'A circular outline or filled circle.',
            'Shapes',
            'shape',
            [
                new ZplAttributeDefinition('thickness', 'number', 'Thickness', true, 3),
                new ZplAttributeDefinition('filled', 'boolean', 'Filled', false, false),
            ],
            self::circleFactory(),
            70,
            70,
            true,
            false,
        ));

        $this->register(new ZplComponentDefinition(
            'line',
            'Horizontal Line',
            'minus',
            'A horizontal line.',
            'Shapes',
            'shape',
            [
                new ZplAttributeDefinition('thickness', 'number', 'Thickness', true, 3),
            ],
            self::lineFactory(),
            120,
            6,
            true,
            false,
        ));

        $this->register(new ZplComponentDefinition(
            'image',
            'Image',
            'image',
            'A raster image converted to ZPL graphics.',
            'Media',
            'image',
            [
                new ZplAttributeDefinition('source', 'image', 'Image', true, ''),
            ],
            self::imageFactory(),
            120,
            120,
            true,
            false,
        ));

        $this->register(new ZplComponentDefinition(
            'table',
            'Table',
            'table',
            'A bordered table rendered by the PHP ZPL library.',
            'Content',
            'table',
            [
                new ZplAttributeDefinition('columns', 'number', 'Columns', true, 2),
                new ZplAttributeDefinition(
                    'rows',
                    'json',
                    'Rows',
                    true,
                    '[["Header 1", "Header 2"], ["Value 1", "Value 2"]]',
                ),
                new ZplAttributeDefinition('fontSize', 'number', 'Font size', true, 24),
                new ZplAttributeDefinition('padding', 'number', 'Padding', true, 4),
                new ZplAttributeDefinition('borderThickness', 'number', 'Border thickness', true, 2),
            ],
            self::tableFactory(),
            320,
            120,
        ));

        $this->register(new ZplComponentDefinition(
            'raw',
            'Raw ZPL',
            'code',
            'Injects a raw ZPL command sequence.',
            'Advanced',
            'code',
            [
                new ZplAttributeDefinition('command', 'text', 'Raw command', true, '^FWN'),
            ],
            self::rawFactory(),
            100,
            30,
            false,
            false,
        ));

        $this->register(new ZplComponentDefinition(
            'vertical-line',
            'Vertical Line',
            'move-vertical',
            'A vertical line.',
            'Shapes',
            'shape',
            [
                new ZplAttributeDefinition('thickness', 'number', 'Thickness', true, 3),
            ],
            self::verticalLineFactory(),
            6,
            120,
            true,
            false,
        ));

        $this->register(new ZplComponentDefinition(
            'ellipse',
            'Ellipse',
            'circle-dashed',
            'An elliptical outline.',
            'Shapes',
            'shape',
            [
                new ZplAttributeDefinition('thickness', 'number', 'Thickness', true, 3),
                new ZplAttributeDefinition('filled', 'boolean', 'Filled', false, false),
            ],
            self::ellipseFactory(),
            140,
            90,
            true,
            false,
        ));

        $this->register(new ZplComponentDefinition(
            'unicode-text',
            'Unicode Text',
            'languages',
            'Unicode text rendered with a bundled TTF font.',
            'Content',
            'text',
            [
                new ZplAttributeDefinition('text', 'text', 'Text', true, 'سلام'),
                new ZplAttributeDefinition('fontName', 'select', 'Font', true, 'A', self::fontOptions()),
                new ZplAttributeDefinition('fontSize', 'number', 'Font size', true, 30),
            ],
            self::unicodeTextFactory(),
            140,
            40,
            true,
            false,
        ));

        $this->register(new ZplComponentDefinition(
            'diagonal-line',
            'Diagonal Line',
            'slash',
            'A diagonal line.',
            'Shapes',
            'shape',
            [
                new ZplAttributeDefinition('thickness', 'number', 'Thickness', true, 3),
                new ZplAttributeDefinition('color', 'select', 'Color', true, 'B', [
                    ['label' => 'Black', 'value' => 'B'],
                    ['label' => 'White', 'value' => 'W'],
                ]),
                new ZplAttributeDefinition('orientation', 'select', 'Orientation', true, 'R', [
                    ['label' => 'Right (\\)', 'value' => 'R'],
                    ['label' => 'Left (/)', 'value' => 'L'],
                ]),
            ],
            self::diagonalLineFactory(),
            120,
            90,
            true,
            false,
        ));

        $this->register(new ZplComponentDefinition(
            'text-group',
            'Group Text Wrapper',
            'rows-3',
            'A wrapper for a group of related text elements.',
            'Content',
            'text',
            [
                new ZplAttributeDefinition('items', 'text', 'Text lines', true, "First line\nSecond line"),
                new ZplAttributeDefinition('fontName', 'select', 'Font', true, 'A', self::fontOptions()),
                new ZplAttributeDefinition('fontSize', 'number', 'Font size', true, 30),
                new ZplAttributeDefinition('orientation', 'select', 'Flow', true, 'v', [
                    ['label' => 'Vertical', 'value' => 'v'],
                    ['label' => 'Horizontal', 'value' => 'h'],
                ]),
                new ZplAttributeDefinition('gap', 'number', 'Gap', true, 5),
            ],
            self::textGroupFactory(),
            160,
            100,
            true,
            false,
        ));
    }

    private static function textFactory(): callable
    {
        return static function (array $instance): RendererInterface {
            $attributes = $instance['attributes'];

            return new Elements\Position(
                (int)($instance['x'] ?? 0),
                (int)($instance['y'] ?? 0),
                new Elements\Text(
                    (string)$attributes['text'],
                    new Elements\Font(
                        (string)$attributes['fontName'],
                        (int)($instance['height'] ?? 30),
                        isset($instance['width']) ? (int)$instance['width'] : null,
                        self::orientationFromRotation((int)($instance['rotation'] ?? 0)),
                    ),
                ),
            );
        };
    }

    private static function barcodeFactory(): callable
    {
        return static function (array $instance): RendererInterface {
            $attributes = $instance['attributes'];
            $options = new Elements\BarcodeOption(self::orientationFromRotation((int)($instance['rotation'] ?? 0)));
            $options->setHeight((int)($instance['height'] ?? 90));

            if ((bool)$attributes['includeLine']) {
                $options->includeLine(true);
            }

            if ((bool)$attributes['lineAbove']) {
                $options->lineAbove(true);
            }

            if ((bool)$attributes['checkDigit']) {
                $options->checkDigit(true);
            }

            $options->setMode((string)$attributes['mode']);

            return (new Elements\Barcode(
                (int)($instance['x'] ?? 0),
                (int)($instance['y'] ?? 0),
                (string)$attributes['value'],
                (int)($instance['height'] ?? 90),
                (int)$attributes['moduleWidth'],
                $options,
            ))->widthRatio((float)$attributes['widthRatio']);
        };
    }

    private static function qrCodeFactory(): callable
    {
        return static function (array $instance): RendererInterface {
            $attributes = $instance['attributes'];

            return new Elements\QrCode(
                (int)($instance['x'] ?? 0),
                (int)($instance['y'] ?? 0),
                (string)$attributes['data'],
                (int)$attributes['magnification'],
                (string)$attributes['errorCorrection'],
            );
        };
    }

    private static function dataMatrixFactory(): callable
    {
        return static function (array $instance): RendererInterface {
            return new Elements\DataMatrix(
                (int)($instance['x'] ?? 0),
                (int)($instance['y'] ?? 0),
                (string)$instance['attributes']['data'],
                (int)($instance['height'] ?? 80),
            );
        };
    }

    private static function boxFactory(): callable
    {
        return static function (array $instance): RendererInterface {
            $attributes = $instance['attributes'];

            return new Elements\Position(
                (int)($instance['x'] ?? 0),
                (int)($instance['y'] ?? 0),
                new Elements\Box(
                    (int)($instance['width'] ?? 120),
                    (int)($instance['height'] ?? 80),
                    (int)$attributes['thickness'],
                    $attributes['color'],
                    (int)$attributes['rounding'],
                ),
            );
        };
    }

    private static function circleFactory(): callable
    {
        return static function (array $instance): RendererInterface {
            $attributes = $instance['attributes'];
            $diameter = (int)($instance['width'] ?? 70);
            $circle = new Elements\Circle(
                $diameter,
                (int)$attributes['thickness'],
                ColorEnums::B,
            );

            if ((bool)$attributes['filled']) {
                $circle->fill();
            }

            return new Elements\Position((int)($instance['x'] ?? 0), (int)($instance['y'] ?? 0), $circle);
        };
    }

    private static function lineFactory(): callable
    {
        return static function (array $instance): RendererInterface {
            return new Elements\Position(
                (int)($instance['x'] ?? 0),
                (int)($instance['y'] ?? 0),
                new Elements\HorizontalLine(
                    (int)($instance['width'] ?? 120),
                    (int)$instance['attributes']['thickness'],
                ),
            );
        };
    }

    private static function rawFactory(): callable
    {
        return static function (array $instance): RendererInterface {
            return new Elements\Raw((string)$instance['attributes']['command']);
        };
    }

    private static function verticalLineFactory(): callable
    {
        return static function (array $instance): RendererInterface {
            return new Elements\Position(
                (int)($instance['x'] ?? 0),
                (int)($instance['y'] ?? 0),
                new Elements\VerticalLine(
                    (int)($instance['height'] ?? 120),
                    (int)$instance['attributes']['thickness'],
                ),
            );
        };
    }

    private static function ellipseFactory(): callable
    {
        return static function (array $instance): RendererInterface {
            $attributes = $instance['attributes'];
            $ellipse = new Elements\Ellipse(
                (int)($instance['width'] ?? 140),
                (int)($instance['height'] ?? 90),
                (int)$attributes['thickness'],
            );

            if ((bool)$attributes['filled']) {
                $ellipse->fill();
            }

            return new Elements\Position((int)($instance['x'] ?? 0), (int)($instance['y'] ?? 0), $ellipse);
        };
    }

    private static function unicodeTextFactory(): callable
    {
        return static function (array $instance): RendererInterface {
            $attributes = $instance['attributes'];

            return new Elements\Position(
                (int)($instance['x'] ?? 0),
                (int)($instance['y'] ?? 0),
                new Elements\UnicodeText(
                    (string)$attributes['text'],
                    new Elements\Font((string)$attributes['fontName'], (int)$attributes['fontSize']),
                ),
            );
        };
    }

    private static function diagonalLineFactory(): callable
    {
        return static function (array $instance): RendererInterface {
            $attributes = $instance['attributes'];

            return new Elements\Position(
                (int)($instance['x'] ?? 0),
                (int)($instance['y'] ?? 0),
                new Elements\DiagonalLine(
                    (int)($instance['width'] ?? 120),
                    (int)($instance['height'] ?? 90),
                    (int)$attributes['thickness'],
                    $attributes['color'],
                    $attributes['orientation'],
                ),
            );
        };
    }

    private static function textGroupFactory(): callable
    {
        return static function (array $instance): RendererInterface {
            $attributes = $instance['attributes'];
            $font = new Elements\Font((string)$attributes['fontName'], (int)$attributes['fontSize']);
            $texts = array_values(array_map(
                static fn (string $line): Elements\Text => new Elements\Text($line, $font),
                array_filter(array_map('trim', explode("\n", (string)$attributes['items'])), static fn (string $line): bool => $line !== ''),
            ));

            $wrapper = (new Elements\Grouping\GroupTextWrapper(
                (string)$attributes['orientation'],
                (int)$attributes['gap'],
                ...$texts,
            ))->font($font);

            return (new Elements\TextGroup(
                (int)($instance['x'] ?? 0),
                (int)($instance['y'] ?? 0),
                (string)$attributes['orientation'],
                (int)$attributes['gap'],
                $wrapper,
            ));
        };
    }

    private static function imageFactory(): callable
    {
        return static function (array $instance): RendererInterface {
            $source = (string)$instance['attributes']['source'];
            $binary = self::decodeImageSource($source);
            $image = new Elements\Image(Decoders\GdDecoder::fromString($binary));

            if (isset($instance['width'])) {
                $image->width((int)$instance['width']);
            }

            if (isset($instance['height'])) {
                $image->height((int)$instance['height']);
            }

            return new Elements\Position((int)($instance['x'] ?? 0), (int)($instance['y'] ?? 0), $image);
        };
    }

    private static function tableFactory(): callable
    {
        return static function (array $instance): RendererInterface {
            $attributes = $instance['attributes'];
            $rows = is_string($attributes['rows'])
                ? json_decode($attributes['rows'], true, 512, JSON_THROW_ON_ERROR)
                : $attributes['rows'];

            return new Elements\Table(
                (int)($instance['x'] ?? 0),
                (int)($instance['y'] ?? 0),
                (int)$attributes['columns'],
                (int)($instance['width'] ?? 320),
                is_array($rows) ? $rows : [],
                [],
                [],
                [
                    'font' => new Elements\Font('0', (int)$attributes['fontSize']),
                    'padding' => (int)$attributes['padding'],
                    'border_thickness' => (int)$attributes['borderThickness'],
                ],
            );
        };
    }

    private static function decodeImageSource(string $source): string
    {
        if ($source === '') {
            throw new InvalidArgumentException('An image source is required.');
        }

        if (preg_match('/^data:image\/[a-z0-9.+-]+;base64,(?P<data>.+)$/i', $source, $matches)) {
            $binary = base64_decode($matches['data'], true);

            if ($binary === false) {
                throw new InvalidArgumentException('The image data URL is invalid.');
            }

            return $binary;
        }

        if (str_contains($source, '://')) {
            throw new InvalidArgumentException('Remote image URLs are not supported. Upload image data instead.');
        }

        if (!is_file($source) || !is_readable($source)) {
            throw new InvalidArgumentException('The image file could not be read.');
        }

        $binary = file_get_contents($source);

        if ($binary === false) {
            throw new InvalidArgumentException('The image file could not be read.');
        }

        return $binary;
    }

    private static function orientationFromRotation(int $rotation): string
    {
        return match ($rotation) {
            90 => 'R',
            180 => 'I',
            270 => 'B',
            default => 'N',
        };
    }

    /** @return list<array{label: string, value: string}> */
    private static function fontOptions(): array
    {
        $options = [];

        foreach (array_merge(range('A', 'Z'), range(0, 9)) as $font) {
            $options[] = ['label' => "Printer font {$font}", 'value' => (string)$font];
        }

        return $options;
    }
}
