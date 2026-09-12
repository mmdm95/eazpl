<?php

use Eazpl\App\Services\ZplComponentRegistry;
use Eazpl\App\Services\ZplDesignerGenerator;
use Eazpl\App\Services\ZplPreviewRenderer;
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

it('generates tables with more than two columns', function () {
    $state = [
        'label' => ['width' => 812, 'height' => 500, 'dpi' => 203, 'orientation' => 'portrait'],
        'components' => [[
            'id' => 'table-instance',
            'type' => 'table',
            'x' => 10,
            'y' => 10,
            'attributes' => [
                'columns' => 4,
                'rows' => '[["A", "B", "C", "D"], ["1", "2", "3", "4"]]',
                'fontSize' => 24,
                'padding' => 4,
                'borderThickness' => 2,
            ],
        ]],
        'selectedComponentId' => 'table-instance',
        'activeTool' => 'selection',
        'zoom' => 1,
        'grid' => ['enabled' => true, 'size' => 10, 'snap' => true],
    ];

    $state = (new DesignerStateValidator())->validate($state);
    $zpl = (new ZplDesignerGenerator(new ZplComponentRegistry()))->generate($state);

    expect($zpl)->toContain('^FDA')
        ->toContain('^FDB')
        ->toContain('^FDC')
        ->toContain('^FDD');
});

it('omits hidden designer layers from generated ZPL', function () {
    $state = [
        'label' => ['width' => 812, 'height' => 500, 'dpi' => 203, 'orientation' => 'portrait'],
        'components' => [[
            'id' => 'hidden-text',
            'type' => 'text',
            'x' => 20,
            'y' => 30,
            'width' => 120,
            'height' => 30,
            'rotation' => 0,
            'visible' => false,
            'locked' => true,
            'attributes' => ['text' => 'Hidden', 'fontName' => '0'],
        ]],
        'selectedComponentId' => null,
        'activeTool' => 'selection',
        'zoom' => 1,
        'grid' => ['enabled' => true, 'size' => 10, 'snap' => true],
    ];

    $zpl = (new ZplDesignerGenerator(new ZplComponentRegistry()))->generate($state);

    expect($zpl)->not->toContain('^FDHidden');
});

it('rejects unsupported preview DPI values', function () {
    (new ZplPreviewRenderer())->render('^XA^XZ', ['width' => 812, 'height' => 500, 'dpi' => 150]);
})->throws(InvalidArgumentException::class, 'The label DPI is not supported by the preview renderer.');

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
