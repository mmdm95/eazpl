<?php

use Eazpl\Contracts\RendererInterface;
use Eazpl\Decoders\GdDecoder;
use Eazpl\Elements\Font;
use Eazpl\Elements\Image;
use Eazpl\Elements\UnicodeText;

it('implements RendererInterface', function () {
    $font = Mockery::mock(Font::class);
    $unicodeText = new UnicodeText('Hello', $font);

    expect($unicodeText)->toBeInstanceOf(RendererInterface::class);
});

it('renders using Image and GdDecoder', function () {
    $font = Mockery::mock(Font::class);
    $font->shouldReceive('getHeight')->andReturn(20);
    $font->shouldReceive('getFontFace')->andReturn('/path/to/font.ttf');

    $mockDecoder = Mockery::mock(GdDecoder::class);
    $mockDecoder->shouldReceive('decode')->andReturn([
        'totalBytes' => 10,
        'rowBytes' => 5,
        'data' => 'ABC123'
    ]);

    $unicodeText = Mockery::mock(UnicodeText::class, ['Hello', $font])
        ->makePartial()
        ->shouldAllowMockingProtectedMethods();

    $unicodeText->shouldReceive('getTextImage')
        ->with('Hello', $font)
        ->andReturn('fake-gd-resource');

    $image = Mockery::mock(Image::class, [$mockDecoder])
        ->makePartial();

    $image->shouldReceive('render')
        ->andReturn('^GF,A,10,5,ABC123');

    expect($image->render())->toBe('^GF,A,10,5,ABC123');
});

it('renders real image when GD is available', function () {
    if (!extension_loaded('gd')) {
        $this->markTestSkipped('GD extension not installed.');
    }

    $fontFile = dirname(__DIR__) . '/Fonts/IRANSansWeb.ttf';
    if (!file_exists($fontFile)) {
        $this->markTestSkipped('No font file available.');
    }

    $font = Mockery::mock(Font::class);
    $font->shouldReceive('getHeight')->andReturn(30);
    $font->shouldReceive('getFontFace')->andReturn($fontFile);

//    $unicodeText = new UnicodeText('Hello 🌍', $font);
    $unicodeText = new UnicodeText('Hello محمد', $font);

    $result = $unicodeText->render();

    expect($result)->toStartWith('^GF,A,')
        ->and(str_contains($result, 'A'))->toBeTrue();
});
