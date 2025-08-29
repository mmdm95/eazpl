<?php

use Eazpl\Elements\VerticalLine;

it('renders vertical line correctly', function () {
    $line = new VerticalLine(50, 2);

    expect($line->render())->toBe('^GB3,50,2');
});

it('renders vertical line with custom thickness', function () {
    $line = new VerticalLine(50, 5);

    expect($line->render())->toBe('^GB5,50,5');
});
