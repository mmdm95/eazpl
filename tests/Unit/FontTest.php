<?php

use Eazpl\Elements\Font;

it('renders font with height only', function () {
    $font = new Font('A', 30);

    expect($font->render())->toBe('^A30');
    expect($font->getHeight())->toBe(30);
    expect($font->getWidth())->toBeNull();
});

it('renders font with height and width', function () {
    $font = new Font('B', 40, 20);

    expect($font->render())->toBe('^B40,20');
    expect($font->getHeight())->toBe(40);
    expect($font->getWidth())->toBe(20);
});

it('accepts numeric font name', function () {
    $font = new Font('3', 25, 15);

    expect($font->render())->toBe('^325,15');
});

it('throws if font name is invalid', function () {
    new Font('@', 30);
})->throws(InvalidArgumentException::class, 'Font name must be 0-9 or A-Z');
