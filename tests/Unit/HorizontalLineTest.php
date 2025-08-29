<?php

use Eazpl\Elements\HorizontalLine;

it('renders horizontal line with default thickness', function () {
    $line = new HorizontalLine(100);

    expect($line->render())->toBe('^GB100,3,3');
});

it('renders horizontal line with custom thickness', function () {
    $line = new HorizontalLine(200, 5);

    expect($line->render())->toBe('^GB200,5,5');
});
