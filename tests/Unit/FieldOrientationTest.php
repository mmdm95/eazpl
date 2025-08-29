<?php

use Eazpl\Elements\FieldOrientation;
use Eazpl\Enums\AlignmentEnums;
use Eazpl\Enums\FieldOrientationEnums;

it('renders with orientation only', function () {
    $element = new FieldOrientation(FieldOrientationEnums::_0, null);

    expect($element->render())->toBe('^FWN');
});

it('renders with orientation and alignment', function () {
    $element = new FieldOrientation(FieldOrientationEnums::_90, AlignmentEnums::RIGHT);

    expect($element->render())->toBe('^FWR,1');
});

it('accepts string orientation and int alignment', function () {
    $element = new FieldOrientation('I', 0);

    expect($element->render())->toBe('^FWI,0');
});

it('skips alignment if null', function () {
    $element = new FieldOrientation(FieldOrientationEnums::_270, null);

    expect($element->render())->toBe('^FWB');
});

it('throws if orientation string is invalid', function () {
    new FieldOrientation('Z', null);
})->throws(InvalidArgumentException::class);

it('throws if alignment int is invalid', function () {
    new FieldOrientation(FieldOrientationEnums::_0, 99);
})->throws(InvalidArgumentException::class);
