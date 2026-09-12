<?php

use Eazpl\App\Services\ZplComponentRegistry;
use Eazpl\App\Services\ZplDesignerGenerator;
use Eazpl\App\Validation\DesignerStateValidator;

it('exposes backend-driven ZPL component definitions', function () {
    $registry = new ZplComponentRegistry();
    $definitions = $registry->all();

    expect($definitions)->not->toBeEmpty()
        ->and($definitions[0]->type)->toBe('text')
        ->and($definitions[0]->attributes)->not->toBeEmpty();
});

it('generates ZPL from designer state', function () {
    $state = [
        'label' => [
            'width' => 812,
            'height' => 500,
            'dpi' => 203,
            'orientation' => 'portrait',
        ],
        'components' => [
            [
                'id' => 'text-instance',
                'type' => 'text',
                'x' => 20,
                'y' => 30,
                'width' => 120,
                'height' => 30,
                'rotation' => 0,
                'attributes' => [
                    'text' => 'Hello',
                    'fontName' => '0',
                    'orientation' => 'N',
                ],
            ],
        ],
        'selectedComponentId' => 'text-instance',
        'zoom' => 1,
        'grid' => ['enabled' => true, 'size' => 10, 'snap' => true],
    ];

    $zpl = (new ZplDesignerGenerator(new ZplComponentRegistry()))->generate($state);

    expect($zpl)->toContain('^FO20,30')
        ->toContain('^FDHello')
        ->toContain('^XZ');
});

it('rejects invalid designer state', function () {
    $validator = new DesignerStateValidator();

    expect(fn () => $validator->validate([
        'label' => ['width' => 812, 'height' => 500, 'dpi' => 999],
        'components' => [],
        'selectedComponentId' => null,
        'zoom' => 1,
        'grid' => ['enabled' => true, 'size' => 10, 'snap' => true],
    ]))->toThrow(Respect\Validation\Exceptions\NestedValidationException::class);
});
