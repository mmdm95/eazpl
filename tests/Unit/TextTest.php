<?php

use Eazpl\Elements\Text;

it('renders text correctly', function () {
    $text = new Text('Hello World');

    expect($text->render())->toBe('^FDHello World');
});
