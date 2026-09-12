# eaZPL

A convenient, object-oriented PHP library for building [ZPL II](https://www.zebra.com/us/en/support-downloads/knowledge-articles/what-is-zpl-ii.html) commands for Zebra and ZPL-compatible label printers.

Instead of concatenating cryptic `^`-commands by hand, you compose labels from PHP objects and let eaZPL generate the ZPL for you:

```php
use Eazpl\Elements\PrintWidth;
use Eazpl\Elements\Position;
use Eazpl\Elements\QrCode;
use Eazpl\Elements\Text;
use Eazpl\ZplPrinter;

$zpl = ZplPrinter::make()
    ->addElements(
        new PrintWidth(812),
        new Position(20, 20, new Text('Hello, Zebra!')),
        new QrCode(20, 80, 'https://example.com'),
    )
    ->build();

echo $zpl;
```

```zpl
^XA

^PW812^FO20,20^FDHello, Zebra!^FS
^FO20,80^BQN,2,2,M^FDhttps://example.com^FS

^XZ
```

## Features

- **Fluent label builder** - add elements, build the ZPL string, or send it straight to a network printer.
- **Rich element set** - text, fonts, blocks, tables, barcodes (Code 128, Code 39, Data Matrix, QR), shapes, lines, and images.
- **Unicode text** - renders non-ASCII text (e.g. Persian, Arabic) as a printer-ready graphic using a TTF font and GD.
- **Images** - converts PNG/JPG/GIF/etc. to compressed ZPL graphics (`^GFA`) via GD, with optional resizing.
- **Layout helpers** - positioning, field blocks, text groups with automatic measurement, and bordered tables with wrapping cells.
- **Printer configuration** - print width, quantity, speed, darkness, orientation, mirroring, label home, and more.
- **Escape hatch** - drop down to raw ZPL any time with `Raw`.

## Requirements

- PHP 8.2 or newer
- PHP extensions: `gd`, `sockets`

## Installation

### Composer

```bash
composer require mmdm/eazpl
```

### Manual installation

Download or clone this repository into your project, then include the bundled autoloader:

```php
require_once 'path/to/eazpl/autoloader.php';
```

## Quick Start

```php
use Eazpl\Components\PlusMinus;
use Eazpl\Elements\Barcode;
use Eazpl\Elements\Box;
use Eazpl\Elements\Font;
use Eazpl\Elements\Position;
use Eazpl\Elements\Text;
use Eazpl\ZplPrinter;

$printer = ZplPrinter::make();

$font = new Font('0', 40); // built-in font "0", 40 dots high

$printer->addElements(
    new Position(50, 50, new Text('MERGE NO', new Font('A', 30))),
    new Position(50, 90, new Text('054552', $font)),
    new Position(50, 150, new Box(400, 100, 3)),
    new Barcode(50, 280, '65854812394812334422', 150, 3),
    new PlusMinus(500, 280, size: 30, thickness: 4, gap: 4),
);

echo $printer->build();
```

### Sending to a printer

```php
$printer = ZplPrinter::make('192.168.1.50', 9100);
// ... add elements ...
$printer->send(); // opens a TCP connection and writes the ZPL
```

The default printer port is `9100`. You can also set the connection details later:

```php
$printer
    ->setIpAddress('192.168.1.50')
    ->setPort(9100);
```

## Visual ZPL Designer

The bundled viewer is a Vue 3 + Tailwind CSS drag-and-drop designer. It discovers component definitions from PHP, so adding a component to `src/App/Services/ZplComponentRegistry.php` makes it available in the UI without frontend changes.

Run the API and viewer in two terminals:

```bash
php bin/eazpl serve
```

```bash
cd viewer
npm install
npm run dev
```

The Vite development server proxies `/api` to the PHP API. The public endpoints are:

| Method | Endpoint | Description |
|---|---|---|
| `GET` | `/api/zpl/components` | Return backend-defined components and attribute metadata |
| `POST` | `/api/zpl/generate` | Validate designer state and generate ZPL with the PHP library |

### Application Commands

```bash
php bin/eazpl migrate          # run pending migrations
php bin/eazpl migrate:reset    # roll back all migrations
php bin/eazpl migrate:status   # show applied and pending migrations
php bin/eazpl serve            # start the PHP API server
```

The same commands are available through Composer:

```bash
composer eazpl:migrate
composer eazpl:reset
composer eazpl:status
composer eazpl:serve
```

Database defaults target local MySQL (`127.0.0.1`, `eazpl`, `root`). Override them with `EAZPL_DB_DRIVER`, `EAZPL_DB_HOST`, `EAZPL_DB_PORT`, `EAZPL_DB_DATABASE`, `EAZPL_DB_USERNAME`, and `EAZPL_DB_PASSWORD`.

## Core Concepts

### ZplPrinter

`ZplPrinter` is the entry point. It wraps your elements between `^XA` (start format) and `^XZ` (end format).

| Method | Description |
|---|---|
| `ZplPrinter::make(?string $ipAddress = null, int $port = 9100)` | Create a new printer instance |
| `setIpAddress(string $ipAddress)` | Set the printer IP for `send()` |
| `setPort(int $port)` | Set the printer port for `send()` |
| `addElement(RendererInterface $element)` | Add one element |
| `addElements(RendererInterface ...$elements)` | Add multiple elements |
| `build(): string` | Build the complete ZPL string |
| `send(): bool` | Validate the connection details and send the ZPL to the printer over TCP |

`ZplPrinter` also implements `__toString()` and `JsonSerializable`, so you can `echo $printer` or `json_encode($printer)` directly.

### Elements

Every printable piece of a label is an object implementing `Eazpl\Contracts\RendererInterface` with a `render(): string` method. Elements are added in order; the ZPL commands appear in the same order.

### Position

`Position` places elements at an `(x, y)` coordinate (in dots) and closes the field with `^FS`:

```php
use Eazpl\Elements\Position;
use Eazpl\Elements\Text;

new Position(100, 200, new Text('At 100,200'));
```

By default the coordinate is the top-left corner. Use `coordinator()` to switch to the bottom-left corner, and `alignment()` to align the field within its width:

```php
use Eazpl\Enums\AlignmentEnums;
use Eazpl\Enums\CoordinationEnums;

new Position(100, 200, new Text('Aligned'))
    ->coordinator(CoordinationEnums::BOTTOM_LEFT_CORNER)
    ->alignment(AlignmentEnums::RIGHT);
```

## Text

### Text

```php
use Eazpl\Elements\Font;
use Eazpl\Elements\Text;

new Text('Hello');                         // default font
new Text('Hello', new Font('0', 40));      // custom font
```

### Font

```php
new Font(
    fontName: '0',          // built-in font: '0'-'9' or 'A'-'Z'
    height: 40,             // height in dots
    width: 30,              // optional width in dots
    orientation: 'N',       // optional: N, R, I, B
    fontFacePath: null,     // optional TTF path, used for UnicodeText
);
```

To use a font stored on the printer (e.g. `E:ARIAL.FNT`):

```php
(new Font('A', 40))->printerPath('E:ARIAL.FNT');
```

### UnicodeText

Renders any UTF-8 text as a monochrome graphic using the font's TTF file, so it works even if the printer has no font for that script:

```php
use Eazpl\Elements\Font;
use Eazpl\Elements\Position;
use Eazpl\Elements\UnicodeText;

new Position(50, 50, new UnicodeText('سلام دنیا', new Font('A', 40)));
```

By default `IRANSansWeb.ttf` (bundled in `src/Fonts/`) is used. Pass your own TTF via `fontFacePath` on the `Font`.

### TextBlock

Sets a default font (`^CF`) for everything inside the block:

```php
use Eazpl\Elements\Position;
use Eazpl\Elements\Text;
use Eazpl\Elements\TextBlock;

new TextBlock(
    new Font('0', 30),
    new Position(50, 50, new Text('Uses the block font')),
);
```

### FieldBlock

Wraps text to a maximum width (`^FB`) with line count, spacing, alignment, and hanging indent:

```php
use Eazpl\Elements\FieldBlock;
use Eazpl\Elements\Text;
use Eazpl\Enums\AlignmentEnums;

new FieldBlock(
    width: 400,          // maximum block width in dots
    maxLines: 2,         // maximum number of lines
    lineSpacing: 10,     // extra dots between lines
    alignment: AlignmentEnums::LEFT,
    hangingIndent: 20,
    elements: new Text('A long text that wraps automatically'),
);
```

### TextGroup and GroupTextWrapper

Lay out multiple `Text` elements horizontally or vertically with a gap, and nest groups inside each other. Bounds are measured automatically:

```php
use Eazpl\Elements\Font;
use Eazpl\Elements\Grouping\GroupTextWrapper;
use Eazpl\Elements\Text;
use Eazpl\Elements\TextGroup;

$font = new Font('0', 40);

$group = new TextGroup(
    50, 50, 'h', 10, // x, y, orientation ('h' or 'v'), gap
    new Text('885', $font),
    new GroupTextWrapper('v', 10, new Text('+', $font), new Text('-', $font)),
    new Text('1', $font),
);

$group->render();
$group->getMaxX(); // right bound
$group->getMaxY(); // bottom bound
```

## Barcodes

### Barcode (Code 128)

```php
use Eazpl\Elements\Barcode;
use Eazpl\Elements\BarcodeOption;

new Barcode(
    x: 20,
    y: 100,
    code: '65854812394812334422',
    height: 150,
    width: 3,              // narrow bar width in dots
    options: new BarcodeOption(
        orientation: 'N',  // N, R, I, B
        height: 150,
        includeLine: true, // print the human-readable line
        lineAbove: false,
        checkDigit: false,
        mode: null,        // N, U, A, D
    ),
);
```

You can also adjust the wide-to-narrow ratio:

```php
(new Barcode(20, 100, '123456', 150, 3))->widthRatio(2.5);
```

### Code 39

```php
use Eazpl\Elements\Code39;

new Code39(
    x: 20,
    y: 100,
    data: 'ABC-1234',
    height: 150,
    includeLine: true,
    lineAbove: false,
    checkDigit: false,
);
```

### DataMatrix

```php
use Eazpl\Elements\DataMatrix;

new DataMatrix(x: 20, y: 100, data: 'ABC-1234', height: 120, quality: 200);
```

> Currently only quality `200` is supported (Labelary-compatible).

### QR Code

```php
use Eazpl\Elements\QrCode;
use Eazpl\Enums\QrErrorCorrectionEnums;

new QrCode(
    x: 20,
    y: 100,
    data: 'https://example.com',
    magnification: 4,                                    // 1-10
    errorCorrection: QrErrorCorrectionEnums::MEDIUM,     // H, Q, M, L
    mask: null,                                          // 0-7, optional
);
```

## Shapes and Lines

All shapes are placed with `Position`:

```php
use Eazpl\Elements\Box;
use Eazpl\Elements\Circle;
use Eazpl\Elements\DiagonalLine;
use Eazpl\Elements\Ellipse;
use Eazpl\Elements\HorizontalLine;
use Eazpl\Elements\Position;
use Eazpl\Elements\VerticalLine;
use Eazpl\Enums\ColorEnums;
use Eazpl\Enums\LineOrientationEnums;

new Position(20, 20, new Box(400, 200, 3, ColorEnums::B, rounding: 4)); // ^GB
new Position(20, 20, new Circle(100, 3)->fill());                       // ^GC
new Position(20, 20, new Ellipse(200, 100, 3)->fill());                 // ^GE
new Position(20, 20, new HorizontalLine(400, 3));                       // horizontal ^GB
new Position(20, 20, new VerticalLine(200, 3));                         // vertical ^GB
new Position(
    20, 20,
    new DiagonalLine(200, 100, 3, ColorEnums::B, LineOrientationEnums::TOP_TO_BOTTOM), // ^GD
);
```

- `ColorEnums::B` = black, `ColorEnums::W` = white
- `fill()` turns a circle/ellipse into a solid shape
- `DiagonalLine` orientation: `TOP_TO_BOTTOM` (`L`) or `BOTTOM_TO_TOP` (`R`)

### PlusMinus component

A ready-made `±` style symbol built from boxes - handy for weight/tolerance labels:

```php
use Eazpl\Components\PlusMinus;

new PlusMinus(100, 100, size: 30, thickness: 4, gap: 4);
```

### ReversePrint

Prints everything inside it in reverse video (`^FR`) - white on black:

```php
use Eazpl\Elements\Position;
use Eazpl\Elements\ReversePrint;
use Eazpl\Elements\Text;

new ReversePrint(
    new Position(50, 50, new Text('White on black')),
);
```

## Images

Images are converted to ZPL graphics (`^GFA`) using GD. Supported formats are everything `imagecreatefromstring()` can read (PNG, JPEG, GIF, WebP, BMP, etc.):

```php
use Eazpl\Decoders\GdDecoder;
use Eazpl\Elements\Image;
use Eazpl\Elements\Position;

$image = new Image(GdDecoder::fromPath('path/to/logo.png'));
$image->height(130); // optional: resize (keeps aspect ratio)
// $image->width(200); // or resize by width

new Position(50, 50, $image);
```

You can also create a decoder from raw binary data or an existing GD image:

```php
GdDecoder::fromString($binaryImageData);
new GdDecoder($gdImage);
```

## Tables

`Table` renders bordered tables with automatic text wrapping, per-cell fonts, custom widths, and padding:

```php
use Eazpl\Elements\Font;
use Eazpl\Elements\Table;

$table = new Table(
    x: 20,
    y: 20,
    colsCount: 3,
    tableWidth: 760,
    rows: [
        ['Grade', 'Filament', 'Den'],
        ['B', '48', '250'],
    ],
    cellsOptions: [
        0 => [0 => ['font' => new Font('A', 25)]], // header styling
    ],
    tableOptions: [
        'font' => new Font('0', 40),
        'padding' => 7,
        'border_thickness' => 3,
    ],
);

$table->render();
$table->getRenderedHeight(); // total height in dots
```

Available options (for `tableOptions`, `rowsOptions`, and `cellsOptions`):

| Option | Description |
|---|---|
| `font` | `Font` used for cell text |
| `height` | Cell/row height in dots |
| `width` | Cell width in dots (fills the remaining width automatically if omitted) |
| `padding` | Inner cell padding in dots |
| `border` / `border_bottom` / `border_right` | Toggle borders |
| `border_thickness` | Border line thickness in dots |

Cells can be plain strings, `['text' => '...', 'font' => $font]` arrays, or any `RendererInterface` element. Non-ASCII cell text is automatically rendered as a graphic.

## Label and Printer Configuration

These elements configure the label format and printer behavior:

| Element | ZPL | Purpose |
|---|---|---|
| `PrintWidth(812)` | `^PW` | Print width in dots |
| `LabelLength(1200)` | `^LL` | Label length in dots |
| `LabelHome(10, 0)` | `^LH` | Label home position |
| `LabelTop(20)` | `^LT` | Label shift from top |
| `LabelShift(15)` | `^LS` | Label shift |
| `PrintQuantity(5)` | `^PQ` | Number of labels, pauses, replicates |
| `PrintRate(3, 3, 3)` | `^PR` | Print/slew/backfeed speeds (1-14) |
| `PrintOrientation('R')` | `^PO` | Print orientation (`N`, `R`, `I`, `B`) |
| `PrintMirror(true)` | `^PM` | Mirror print |
| `LabelReversePrint(true)` | `^LR` | Reverse print the whole label |
| `Darkness(15)` | `^MD` | Darkness modifier (-30 to 30) |
| `Mode(ModeEnums::T)` | `^MM` | Print mode (tear, peel, rewind, etc.) |
| `Charset(28)` | `^CI` | International character set (0-36) |
| `FontDefinition('E', 'E:ARIAL.FNT')` | `^CW` | Give a printer-stored font a name |
| `RecallFormat('E:LABEL.ZPL')` | `^XF` | Recall a stored format |
| `RecallGraphics('E:LOGO.GRF', 2, 2)` | `^XG` | Recall stored graphics with magnification |

Example:

```php
use Eazpl\Elements\Darkness;
use Eazpl\Elements\LabelHome;
use Eazpl\Elements\PrintQuantity;
use Eazpl\Elements\PrintRate;
use Eazpl\Elements\PrintWidth;
use Eazpl\Enums\ModeEnums;
use Eazpl\Elements\Mode;

$printer->addElements(
    new PrintWidth(812),
    new LabelHome(10, 0),
    new PrintQuantity(5, noPause: true),
    new PrintRate(3, 3, 3),
    new Darkness(15),
    new Mode(ModeEnums::T),
);
```

## Field Commands

| Element | ZPL | Purpose |
|---|---|---|
| `FieldBlock(400, 2, 10, ...)` | `^FB` | Wrap text into a block |
| `FieldNumber(1)` | `^FN` | Number a field in a stored format |
| `FieldOrientation('R')` | `^FW` | Default field orientation |
| `FieldTypeset(100, 200, ...)` | `^FT` | Position a field (typeset) |
| `FieldVariable('data')` | `^FV` | Variable data for a recalled format |
| `SerialNumber('1', 1, pad: true)` | `^SN` | Auto-incrementing serial numbers |
| `TextBoundingBox(400, 100, ...)` | `^TB` | Text bounding box |
| `HexIndicator()` | `^FH` | Enable hex escape indicator |
| `ReversePrint(...)` | `^FR` | Reverse print nested elements |

## Raw ZPL

If a command is not covered by an element yet, use `Raw`:

```php
use Eazpl\Elements\Comment;
use Eazpl\Elements\Raw;

$printer->addElements(
    new Comment('This is a note in the format'),
    new Raw('^BCN,150,Y,N,N^FD123456^FS'),
);
```

## Enums

Where an element accepts an enum, you can usually also pass the raw string/int value:

| Enum | Values |
|---|---|
| `AlignmentEnums` | `LEFT`, `RIGHT`, `AUTO` |
| `BarcodeModeEnums` | `N`, `U`, `A`, `D` |
| `BoolEnums` | `Y`, `N` (or PHP `true`/`false`) |
| `ColorEnums` | `B` (black), `W` (white) |
| `CoordinationEnums` | `TOP_LEFT_CORNER`, `BOTTOM_LEFT_CORNER` |
| `FieldOrientationEnums` | `_0` (`N`), `_90` (`R`), `_180` (`I`), `_270` (`B`) |
| `LineOrientationEnums` | `BOTTOM_TO_TOP` (`R`), `TOP_TO_BOTTOM` (`L`) |
| `ModeEnums` | `T`, `P`, `R`, `A`, `C`, `D`, `F`, `K` |
| `QrErrorCorrectionEnums` | `HIGH`, `QUARTILE`, `MEDIUM`, `LOW` |

## Error Handling

All library-specific exceptions extend `Eazpl\Exceptions\ZplException`:

| Exception | Thrown when |
|---|---|
| `ConnectionException` | The socket cannot be created or the printer cannot be reached |
| `InvalidIPAddressException` | The IP address is not a valid IPv4/IPv6 address |
| `InvalidPortException` | The port is outside 0-65535 |
| `NeedIPAddressException` | `send()` is called without an IP address |

Invalid element values (coordinates out of range, bad font names, malformed table rows, etc.) throw PHP's `InvalidArgumentException` with a descriptive message.

```php
use Eazpl\Exceptions\ConnectionException;
use Eazpl\Exceptions\ZplException;

try {
    $printer->send();
} catch (ConnectionException $e) {
    // log/handle unreachable printer
} catch (ZplException $e) {
    // other library errors
}
```

## Extending eaZPL

- Implement `Eazpl\Contracts\RendererInterface` (a single `render(): string` method) to create your own elements. Note that `ZplPrinter` only accepts its built-in element types - wrap custom renderers inside `Position`, `Wrapper`, or `ReversePrint`.
- Implement `Eazpl\Contracts\DecoderInterface` to support custom image sources for the `Image` element (`decode`, `resize`, `width`, `height`, `getBitAt`).

## Testing

The test suite uses [Pest](https://pestphp.com/):

```bash
composer install
./vendor/bin/pest
```

## License

eaZPL is open-sourced software licensed under the [MIT license](LICENSE.txt).
