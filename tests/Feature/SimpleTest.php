<?php

use Eazpl\Decoders\GdDecoder;
use Eazpl\Elements\Barcode;
use Eazpl\Elements\Box;
use Eazpl\Elements\Comment;
use Eazpl\Elements\Font;
use Eazpl\Elements\Grouping\GroupTextWrapper;
use Eazpl\Elements\HorizontalLine;
use Eazpl\Elements\Image;
use Eazpl\Elements\Position;
use Eazpl\Elements\Text;
use Eazpl\Elements\TextBlock;
use Eazpl\Elements\TextGroup;
use Eazpl\Elements\VerticalLine;
use Eazpl\ZplPrinter;

it('creates a simple zpl', function () {
    // below code is for a 10x9 cm label
    // 8dpmm, 203dpi --> 1cm => 79.9point

    $printer = new ZplPrinter();

    $bigFont = new Font('0', 50);
    $smallFont = new Font('A', 30);

    $decoder = GdDecoder::fromPath(dirname(__DIR__) . '/assets/logo.png');

    $printer->addElements(
        new Comment('top label'),
        new Position(80, 30, (new Image($decoder))->height(130)),
        new Position(270, 20, new Box(510, 150)),
        new Position(530, 20, new VerticalLine(150)),
        new Position(320, 50, new Text('MERGE NO', $smallFont)),
        new Position(320, 90, new Text('054552', $bigFont)),
        new Position(570, 50, new Text('PALLET NO', $smallFont)),
        new Position(600, 90, new Text('2357', $bigFont)),

        new Position(20, 167, new Box(760, 300)),
        new Position(270, 166, new VerticalLine(300)),
        new Position(530, 166, new VerticalLine(300)),
        new Position(20, 310, new HorizontalLine(760)),
        new Position(60, 230, new Text('Grade', $smallFont)),
        new Position(170, 220, new Text('B', $bigFont)),
        new Position(290, 230, new Text('Filament', $smallFont)),
        new Position(460, 220, new Text('48', $bigFont)),
        new Position(570, 230, new Text('Den', $smallFont)),
        new Position(650, 220, new Text('250', $bigFont)),
        new Position(60, 340, new Text('NO. of', $smallFont)),
        new Position(60, 370, new Text('BOBBINS', $smallFont)),
        new Position(200, 350, new Text('66', $bigFont)),
        new Position(290, 340, new Text('GROSS WEIGHT', $smallFont)),
        new Position(290, 370, new Text('(kg)', $smallFont)),
//        new Position(290, 410, new Text('885 +- 1', $bigFont)),
        new TextGroup(
            290, 410, 'h', 5,
            new Text('885', $bigFont),
            new GroupTextWrapper('v', 5, new Text('+', $bigFont), new Text('-', $bigFont)),
            new Text('1', $bigFont)
        ),
        new Position(570, 340, new Text('NET WEIGHT', $smallFont)),
        new Position(570, 370, new Text('(kg)', $smallFont)),
        new Position(550, 410, new Text('824 +- 3', $bigFont)),

        new Barcode(20, 510, '65854812394812334422', 150, 3),
    );

    $result = $printer->build();

//    file_put_contents(dirname(__DIR__) . '/testing/result.txt', $result);

    expect(true)->toBeTrue();
});

it('created a two column zpl', function () {
    $printer = new ZplPrinter();

    $smallFont = new Font('0', 20);

    for ($i = 1; $i <= 2; ++$i) {
        $x = 40 + (($i - 1) * 395);
        $y = 35;

        $printer->addElements(
            new Comment("column $i"),
            new TextBlock(
                $smallFont,
                new Position($x, $y, new Text('code : 10020S : A')),
                new Position($x + 200, $y += 20, new Text('POY', new Font('0', 40))),
                new Position($x, $y += 20, new Text('Dtex/F : 250/48 :')),
                new Position($x, $y += 20, new Text('------------------')),
                new Position($x, $y += 20, new Text('W/B : 06/00      (12:01:29)')),
                new Barcode($x, $y += 25, new Text('108765432456789890087654354', new Font('A', 20)), 70, 1)
            ),
        );
    }

    $result = $printer->build();

    file_put_contents(dirname(__DIR__) . '/testing/result.txt', $result);

    expect(true)->toBeTrue();
});
