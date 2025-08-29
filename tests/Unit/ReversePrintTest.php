<?php

use Eazpl\Elements\Raw;
use Eazpl\Elements\ReversePrint;

it('renders reverse print with elements', function () {
    $element1 = new Raw('^FO100,100^FDHello^FS');
    $element2 = new Raw('^FO200,200^FDWorld^FS');

    $reverse = new ReversePrint($element1, $element2);

    expect($reverse->render())->toBe('^FR^FO100,100^FDHello^FS^FO200,200^FDWorld^FS');
});
