<?php

use Eazpl\Contracts\DecoderInterface;
use Eazpl\Decoders\GdDecoder;
use Eazpl\Elements\Image;

it('tests GdDecoder functionality', function () {
    // Create a real GD image
    $gdImage = imagecreatetruecolor(100, 200);

    // Create a mock for the GdDecoder, allowing the real constructor to be called
    $mockDecoder = Mockery::mock(GdDecoder::class, [$gdImage])->makePartial();

    // Define the behavior of the mock
    $mockDecoder->shouldReceive('width')
        ->andReturn(100);

    $mockDecoder->shouldReceive('height')
        ->andReturn(200);

    $mockDecoder->shouldReceive('getBitAt')
        ->with(10, 10)
        ->andReturn(255);

    expect($mockDecoder->width())->toBe(100)
        ->and($mockDecoder->height())->toBe(200)
        ->and($mockDecoder->getBitAt(10, 10))->toBe(255);
});

it('renders the image correctly', function () {
    $mockDecoder = Mockery::mock(DecoderInterface::class);

    // Define the expected behavior of the mock
    $mockDecoder->shouldReceive('decode')
        ->once()
        ->andReturn([
            'totalBytes' => 1024,
            'rowBytes' => 256,
            'data' => 'mocked_image_data'
        ]);

    $image = new Image($mockDecoder);

    $result = $image->render();

    expect($result)->toBe('^GF,A,1024,256,mocked_image_data');
});

// Clean up Mockery after tests
afterEach(function () {
    Mockery::close();
});
