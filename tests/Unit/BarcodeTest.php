<?php

use Eazpl\Elements\Barcode;

it('renders a barcode with default width ratio', function () {
    $barcode = new Barcode(100, 200, '123456', 80);

    $rendered = $barcode->render();

    expect($rendered)->toContain('^BY5,2,80')
        ->and($rendered)->toContain('^FO100,200')
        ->and($rendered)->toContain('^BC')
        ->and($rendered)->toContain('^FD123456');
});

it('throws exception if width is less than 1', function () {
    new Barcode(50, 50, 'ABC', 40, 0);
})->throws(InvalidArgumentException::class);

it('throws exception if width is greater than 100', function () {
    new Barcode(50, 50, 'XYZ', 40, 101);
})->throws(InvalidArgumentException::class);

it('respects width ratio clamping (below 2 sets to 3)', function () {
    $barcode = (new Barcode(0, 0, 'TEST', 60))->widthRatio(1.5);
    expect($barcode->render())->toContain('^BY5,3,60');
});

it('respects width ratio clamping (above 3 sets to 3)', function () {
    $barcode = (new Barcode(0, 0, 'TEST', 60))->widthRatio(4);
    expect($barcode->render())->toContain('^BY5,3,60');
});

it('allows valid width ratio of 2.5', function () {
    $barcode = (new Barcode(0, 0, 'TEST', 60))->widthRatio(2.5);
    expect($barcode->render())->toContain('^BY5,2.5,60');
});
