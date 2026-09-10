<?php

use Eazpl\Components\PlusMinus;

it('renders plus minus with default dimensions', function () {
    $plusMinus = new PlusMinus(100, 200);

    expect($plusMinus->render())->toBe(
        '^FO108,200^GB3,20,3^FS' . "\n" .
        '^FO100,208^GB20,3,3^FS' . "\n" .
        '^FO100,223^GB20,3,3^FS' . "\n"
    );
});

it('renders plus minus with custom dimensions and gap', function () {
    $plusMinus = new PlusMinus(20, 30, 12, 2, 1);

    expect($plusMinus->render())->toBe(
        '^FO25,30^GB2,12,2^FS' . "\n" .
        '^FO20,35^GB12,2,2^FS' . "\n" .
        '^FO20,43^GB12,2,2^FS' . "\n"
    );
});

it('throws exception if size is smaller than thickness', function () {
    new PlusMinus(10, 10, 2, 3);
})->throws(InvalidArgumentException::class, 'Size must be greater than or equal to thickness');
