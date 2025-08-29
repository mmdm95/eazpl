<?php

use Eazpl\Elements\Font;
use Eazpl\Elements\Text;
use Eazpl\Elements\TextBlock;

it('renders text block with font and nested elements', function () {
    $font = new Font('A', 10, 5);
    $text = new Text('Hello');
    $textBlock = new TextBlock($font, $text);

    $expected = "\n^CFA,10,5\n^FDHello";
    expect($textBlock->render())->toBe($expected);
});
