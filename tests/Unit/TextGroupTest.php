<?php

use Eazpl\Elements\Font;
use Eazpl\Elements\Grouping\GroupTextWrapper;
use Eazpl\Elements\Text;
use Eazpl\Elements\TextGroup;

it('calculates horizontal text group bounds without a trailing gap', function () {
    $font = new Font('0', 20, 10);
    $group = new TextGroup(10, 20, 'h', 5, new Text('ABC', $font), new Text('DE', $font));

    $group->render();

    expect($group->getMaxX())->toBe(65)
        ->and($group->getMaxY())->toBe(40);
});

it('calculates vertical text group bounds without a trailing gap', function () {
    $font = new Font('0', 20, 10);
    $group = new TextGroup(10, 20, 'v', 5, new Text('ABC', $font), new Text('DE', $font));

    $group->render();

    expect($group->getMaxX())->toBe(40)
        ->and($group->getMaxY())->toBe(65);
});

it('renders a nested vertical group inside a horizontal group', function () {
    $font = new Font('0', 20, 10);
    $group = new TextGroup(
        0,
        0,
        'h',
        5,
        new Text('A', $font),
        new GroupTextWrapper('v', 5, new Text('B', $font), new Text('C', $font)),
        new Text('D', $font)
    );

    $zpl = $group->render();

    expect($zpl)->toContain('^FO0,0^A0,20,10^FDA')
        ->and($zpl)->toContain('^FO15,0^A0,20,10^FDB')
        ->and($zpl)->toContain('^FO15,25^A0,20,10^FDC')
        ->and($zpl)->toContain('^FO30,0^A0,20,10^FDD')
        ->and($group->getMaxX())->toBe(40)
        ->and($group->getMaxY())->toBe(45);
});
