<?php

use Eazpl\Elements\Comment;

it('renders a comment with text', function () {
    $comment = new Comment('This is a test comment');

    expect($comment->render())->toBe("^FX This is a test comment\n");
});

it('renders an empty comment', function () {
    $comment = new Comment('');

    expect($comment->render())->toBe("^FX \n");
});

it('always ends with a newline', function () {
    $comment = new Comment('Another one');

    $output = $comment->render();

    expect(substr($output, -1))->toBe("\n");
});
