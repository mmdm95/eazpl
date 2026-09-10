<?php

use Eazpl\Exceptions\InvalidIPAddressException;
use Eazpl\Exceptions\InvalidPortException;
use Eazpl\Utils\PrinterUtils;

it('accepts valid IPv4 and IPv6 addresses', function (string $ipAddress) {
    expect(PrinterUtils::getValidIpAddressOf($ipAddress))->toBe($ipAddress);
})->with([
    'IPv4' => ['192.168.1.50'],
    'IPv6' => ['::1'],
]);

it('rejects invalid IP addresses', function () {
    PrinterUtils::getValidIpAddressOf('not-an-ip');
})->throws(InvalidIPAddressException::class, 'Please provide a valid IPv4 or IPv6 address.');

it('accepts valid ports', function (int $port) {
    expect(PrinterUtils::getValidPortOf($port))->toBe($port);
})->with([0, 9100, 65535]);

it('rejects invalid ports', function (mixed $port) {
    PrinterUtils::getValidPortOf($port);
})->with([-1, 65536, '9100'])->throws(InvalidPortException::class);
