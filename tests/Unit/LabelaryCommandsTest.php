<?php

use Eazpl\Elements\Code39;
use Eazpl\Elements\DataMatrix;
use Eazpl\Elements\Darkness;
use Eazpl\Elements\FieldBlock;
use Eazpl\Elements\FieldNumber;
use Eazpl\Elements\FieldTypeset;
use Eazpl\Elements\FieldVariable;
use Eazpl\Elements\FontDefinition;
use Eazpl\Elements\Font;
use Eazpl\Elements\LabelHome;
use Eazpl\Elements\LabelLength;
use Eazpl\Elements\LabelShift;
use Eazpl\Elements\LabelTop;
use Eazpl\Elements\LabelReversePrint;
use Eazpl\Elements\Position;
use Eazpl\Elements\PrintMirror;
use Eazpl\Elements\PrintOrientation;
use Eazpl\Elements\PrintQuantity;
use Eazpl\Elements\PrintRate;
use Eazpl\Elements\PrintWidth;
use Eazpl\Elements\QrCode;
use Eazpl\Elements\RecallFormat;
use Eazpl\Elements\RecallGraphics;
use Eazpl\Elements\SerialNumber;
use Eazpl\Elements\Text;
use Eazpl\Elements\TextBoundingBox;
use Eazpl\Enums\FieldOrientationEnums;

it('renders supported label setup commands', function () {
    expect((new LabelHome(10, 20))->render())->toBe('^LH10,20')
        ->and((new LabelLength(320, true))->render())->toBe('^LL320,Y')
        ->and((new LabelShift(5))->render())->toBe('^LS5')
        ->and((new LabelTop(8))->render())->toBe('^LT8')
        ->and((new PrintWidth(812))->render())->toBe('^PW812');
});

it('renders supported print control commands', function () {
    expect((new PrintQuantity(5, noPause: true))->render())->toBe('^PQ5,0,0,Y')
        ->and((new PrintRate(6))->render())->toBe('^PR6')
        ->and((new PrintOrientation(FieldOrientationEnums::_90))->render())->toBe('^POR')
        ->and((new PrintMirror(true))->render())->toBe('^PMY')
        ->and((new Darkness(-5))->render())->toBe('^MD-5');
});

it('renders a field block inside a positioned field', function () {
    $element = new Position(
        10,
        20,
        new FieldBlock(200, 3, 10, 1, 5, new Text('Wrapped text'))
    );

    expect($element->render())->toBe("^FO10,20^FB200,3,10,1,5^FDWrapped text^FS\n");
});

it('renders field typeset, variable, number, and serialization commands', function () {
    expect((new FieldTypeset(30, 40, 1, new Text('Hello')))->render())->toBe("^FT30,40,1^FDHello^FS\n")
        ->and((new FieldVariable('value'))->render())->toBe('^FVvalue')
        ->and((new FieldNumber(12))->render())->toBe('^FN12')
        ->and((new SerialNumber('0001', 2, true))->render())->toBe('^SN0001,2,Y');
});

it('renders supported graphics recall and barcode commands', function () {
    expect((new RecallGraphics('R:LOGO.GRF', 2, 3))->render())->toBe('^XGR:LOGO.GRF,2,3')
        ->and((new Code39(10, 20, 'ABC-123', 50, true, false, true))->render())
        ->toBe("^FO10,20^B3N,Y,50,Y,N^FDABC-123^FS\n")
        ->and((new QrCode(30, 40, 'QR DATA', 4, 'Q', 7))->render())
        ->toBe("^FO30,40^BQN,2,4,Q,7^FDQR DATA^FS\n")
        ->and((new DataMatrix(50, 60, 'DM DATA', 20))->render())
        ->toBe("^FO50,60^BXN,20,200^FDDM DATA^FS\n");
});

it('renders custom font, bounded text, label reverse, and format recall commands', function () {
    expect((new FontDefinition('Z', 'B:CUSTOM.TTF'))->render())->toBe('^CWZ,B:CUSTOM.TTF')
        ->and((new Font('A', 30, 20))->printerPath('B:CUSTOM.TTF')->render())
        ->toBe('^A@N,30,20,B:CUSTOM.TTF')
        ->and((new Position(10, 20, new TextBoundingBox(300, 100, 'R', new Text('Bounded'))))->render())
        ->toBe("^FO10,20^TBR,300,100^FDBounded^FS\n")
        ->and((new LabelReversePrint(true))->render())->toBe('^LRY')
        ->and((new RecallFormat('R:FORM.ZPL'))->render())->toBe('^XFR:FORM.ZPL');
});
