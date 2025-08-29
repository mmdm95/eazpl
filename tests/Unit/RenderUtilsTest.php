<?php

use Eazpl\Elements\Comment;
use Eazpl\Elements\Text;
use Eazpl\Enums\AlignmentEnums;
use Eazpl\Enums\FieldOrientationEnums;
use Eazpl\Utils\RenderUtils;

it('renders insider elements correctly', function () {
    $comment1 = new Comment('First');
    $comment2 = new Comment('Second');
    $text = new Text('Hello');

    $result = RenderUtils::renderInsiderElements([$comment1, $comment2, $text, 'not-renderer']);
    expect($result)->toBe($comment1->render() . $comment2->render() . $text->render());
});

it('validates XY value correctly', function () {
    expect(RenderUtils::getValidXYValue(0))->toBe(0)
        ->and(RenderUtils::getValidXYValue(32_000))->toBe(32_000);
    RenderUtils::getValidXYValue(-1);
})->throws(InvalidArgumentException::class);

it('validates XY value above max', function () {
    RenderUtils::getValidXYValue(32001);
})->throws(InvalidArgumentException::class);

it('returns correct field orientation enum', function () {
    expect(RenderUtils::getValidFieldOrientation(FieldOrientationEnums::_90))->toBe(FieldOrientationEnums::_90)
        ->and(RenderUtils::getValidFieldOrientation('R'))->toBe(FieldOrientationEnums::_90);
});

it('throws on invalid field orientation', function () {
    RenderUtils::getValidFieldOrientation('INVALID');
})->throws(InvalidArgumentException::class);

it('returns correct alignment enum', function () {
    expect(RenderUtils::getValidAlignment(AlignmentEnums::LEFT))->toBe(AlignmentEnums::LEFT)
        ->and(RenderUtils::getValidAlignment(1))->toBe(AlignmentEnums::RIGHT);
});

it('throws on invalid alignment', function () {
    RenderUtils::getValidAlignment(99);
})->throws(InvalidArgumentException::class);
