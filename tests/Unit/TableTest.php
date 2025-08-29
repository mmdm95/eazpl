<?php

use Eazpl\Elements\Table;

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
        ->toContain("^FO7,7^FDH^FS\n^FO7,34^FDe^FS\n^FO7,61^FDl^FS\n^FO7,88^FDl^FS\n^FO7,115^FDo^FS")
        ->toContain("^FO32,7^FDW^FS\n^FO32,34^FDo^FS\n^FO32,61^FDr^FS\n^FO32,88^FDl^FS\n^FO32,115^FDd^FS");
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

    expect($zpl)->toContain('^FO50,50')  // table starts here
    ->and($zpl)->toContain('^CF0,30,20') // font applied
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

    expect($zpl)->toContain('^FO50,50')  // table starts here
    ->and($zpl)->toContain('^CF0,30,20') // font applied
    ->and($zpl)->toContain('^FDAlice')   // cell text
    ->and($zpl)->toContain('^FD30')     // cell text
    ->and($zpl)->toContain('^FDAllen')   // cell text
    ->and($zpl)->toContain('^FD36');     // cell text
});
