<?php

use Eazpl\Elements\BackgroundRectangle;

it('renders a background rectangle with its seven parameters', function () {
    $rectangle = new BackgroundRectangle(10, 20, 300, 200, 255, 255, 0);

    expect($rectangle->render())->toBe('~BR10,20,300,200,255,255,0');
});

it('clears background rectangles when no parameters are provided', function () {
    expect((new BackgroundRectangle())->render())->toBe('~BR');
});
