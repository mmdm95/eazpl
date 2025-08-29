<?php

use Eazpl\Elements\Raw;

it('renders raw string correctly', function () {
    $raw = new Raw('^FO100,100^FDHello^FS');
    expect($raw->render())->toBe('^FO100,100^FDHello^FS');
});
