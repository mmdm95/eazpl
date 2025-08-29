<?php

use Eazpl\Elements\Comment;
use Eazpl\Elements\Position;
use Eazpl\Enums\AlignmentEnums;

it('renders position with default alignment and no children', function () {
    $pos = new Position(100, 200);

    expect($pos->render())->toBe('^FO100,200^FS' . "\n");
});

it('renders position with custom alignment (RIGHT)', function () {
    $pos = new Position(50, 60);
    $pos->alignment(AlignmentEnums::RIGHT);

    expect($pos->render())->toBe('^FO50,60,1^FS' . "\n");
});

it('renders position with nested elements', function () {
    $comment = new Comment('Hello');
    $pos = new Position(10, 20, $comment);

    expect($pos->render())->toBe('^FO10,20' . $comment->render() . '^FS' . "\n");
});

it('accepts integer alignment', function () {
    $pos = new Position(30, 40);
    $pos->alignment(2); // AUTO

    expect($pos->render())->toBe('^FO30,40,2^FS' . "\n");
});
