<?php

use Eazpl\Elements\Table;
use Eazpl\Elements\Font;

it('throws exception if column count is less than 1', function () {
    new Table(0, 0, 0, 50, tableOptions: ['height' => 20]);
})->throws(InvalidArgumentException::class, 'Table must have at least 1 column.');

it('throws exception if adding a row with invalid column count', function () {
    $table = new Table(0, 0, 2, 50, tableOptions: ['height' => 20]);
    $table->addRow(['only one column']);
})->throws(InvalidArgumentException::class, 'Row must have exactly 2 columns.');

it('addRow returns self for chaining', function () {
    $table = new Table(0, 0, 2, 50, tableOptions: ['height' => 20]);
    $result = $table->addRow(['col1', 'col2']);
    expect($result)->toBe($table);
});

it('renders table with string cells', function () {
    $table = new Table(0, 0, 2, 50, tableOptions: ['height' => 30]);
    $table->addRow(['Hello', 'World']);

    $zpl = $table->render();

    expect($zpl)->toBeString()
        ->toContain("^FO7,7^AA,27^FDH^FS\n^FO7,34^AA,27^FDe^FS\n^FO7,61^AA,27^FDl^FS\n^FO7,88^AA,27^FDl^FS\n^FO7,115^AA,27^FDo^FS")
        ->toContain("^FO32,7^AA,27^FDW^FS\n^FO32,34^AA,27^FDo^FS\n^FO32,61^AA,27^FDr^FS\n^FO32,88^AA,27^FDl^FS\n^FO32,115^AA,27^FDd^FS");
});

it('renders a simple table with 1 row', function () {
    $table = new Table(
        x: 50,
        y: 50,
        colsCount: 2,
        tableWidth: 750,
        rows: [['Alice', '30']],
        tableOptions: ['font' => fontDefault()]
    );

    $zpl = $table->render();

    expect($zpl)->toContain('^FO50,50^GB750,44,3') // table starts here
    ->and($zpl)->toContain('^A0,30,20') // font applied
    ->and($zpl)->toContain('^FDAlice')   // cell text
    ->and($zpl)->toContain('^FD30');     // cell text
});

it('renders a simple table with 2 rows', function () {
    $table = new Table(
        x: 50,
        y: 50,
        colsCount: 2,
        tableWidth: 750,
        rows: [['Alice', '30'], ['Allen', '36']],
        tableOptions: ['font' => fontDefault()]
    );

    $zpl = $table->render();

    expect($zpl)->toContain('^FO50,50^GB750,91,3') // table starts here
    ->and($zpl)->toContain('^A0,30,20') // font applied
    ->and($zpl)->toContain('^FDAlice')   // cell text
    ->and($zpl)->toContain('^FD30')     // cell text
    ->and($zpl)->toContain('^FDAllen')   // cell text
    ->and($zpl)->toContain('^FD36');     // cell text
});

it('renders an empty table without commands', function () {
    $table = new Table(10, 10, 2, 100);

    expect($table->render())->toBe('')
        ->and($table->getRenderedHeight())->toBe(0);
});

it('wraps cell text using the available cell width', function () {
    $table = new Table(
        x: 0,
        y: 0,
        colsCount: 1,
        tableWidth: 120,
        rows: [['ABCDEFGHIJ KLMNOPQRST']],
        tableOptions: [
            'font' => new Font('0', 20, 10),
            'padding' => 5,
        ]
    );

    $zpl = $table->render();

    expect($zpl)->toContain('^FO5,5^A0,20,10^FDABCDEFGHIJ^FS')
        ->and($zpl)->toContain('^FO5,25^A0,20,10^FDKLMNOPQRST^FS')
        ->and($table->getRenderedHeight())->toBe(50);
});

it('supports custom cell widths while filling the remaining table width', function () {
    $table = new Table(
        x: 10,
        y: 10,
        colsCount: 3,
        tableWidth: 300,
        rows: [['A', 'B', 'C']],
        cellsOptions: [[1 => ['width' => 100]]],
        tableOptions: ['font' => new Font('0', 20, 10), 'padding' => 5]
    );

    $zpl = $table->render();

    expect($zpl)->toContain('^FO15,15^A0,20,10^FDA^FS')
        ->and($zpl)->toContain('^FO115,15^A0,20,10^FDB^FS')
        ->and($zpl)->toContain('^FO215,15^A0,20,10^FDC^FS')
        ->and($zpl)->toContain('^FO110,10^GB3,33,3^FS')
        ->and($zpl)->toContain('^FO210,10^GB3,33,3^FS');
});

it('preserves explicit cell line breaks', function () {
    $table = new Table(
        x: 0,
        y: 0,
        colsCount: 1,
        tableWidth: 60,
        rows: [["AA\nBBBB"]],
        tableOptions: [
            'font' => new Font('0', 20, 10),
            'padding' => 5,
        ]
    );

    $zpl = $table->render();

    expect($zpl)->toContain('^FO5,5^A0,20,10^FDAA^FS')
        ->and($zpl)->toContain('^FO5,25^A0,20,10^FDBBBB^FS')
        ->and($table->getRenderedHeight())->toBe(50);
});

it('renders non-ASCII cell text as a graphic', function () {
    $table = new Table(
        x: 0,
        y: 0,
        colsCount: 1,
        tableWidth: 120,
        rows: [['سلام']],
        tableOptions: [
            'font' => new Font('A', 20, 10),
            'padding' => 5,
        ]
    );

    expect($table->render())->toContain('^FO5,5^GFA,');
});
