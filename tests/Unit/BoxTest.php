<?php

use Eazpl\Elements\Box;

it('renders a box with valid dimensions', function () {
    $box = new Box(200, 100, 3);

    expect($box->render())->toBe('^GB200,100,3');
});

it('uses default thickness when not provided', function () {
    $box = new Box(150, 150);

    expect($box->render())->toBe('^GB150,150,3');
});

it('throws exception if width is smaller than thickness', function () {
    new Box(2, 100, 3);
})->throws(InvalidArgumentException::class, 'Width must be greater than or equal to 3');

it('throws exception if height is smaller than thickness', function () {
    new Box(100, 2, 5);
})->throws(InvalidArgumentException::class, 'Height must be greater than or equal to 5');

it('throws exception if width is greater than 32000', function () {
    new Box(32_001, 100, 3);
})->throws(InvalidArgumentException::class);

it('throws exception if height is greater than 32000', function () {
    new Box(100, 32_001, 3);
})->throws(InvalidArgumentException::class);

it('throws exception if thickness is greater than 32000', function () {
    new Box(100, 100, 32_001);
})->throws(InvalidArgumentException::class);
