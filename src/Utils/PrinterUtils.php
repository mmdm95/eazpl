<?php

namespace Eazpl\Utils;

use Eazpl\Exceptions\InvalidIPAddressException;
use Eazpl\Exceptions\InvalidPortException;

class PrinterUtils
{
    /**
     * @param string $ipAddress
     * @return string
     * @throws InvalidIPAddressException
     */
    public static function getValidIpAddressOf(string $ipAddress): string
    {
        $ipAddress = trim($ipAddress);

        if (false === filter_var($ipAddress, FILTER_VALIDATE_IP)) {
            throw new InvalidIPAddressException('Please provide a valid IPv4 or IPv6 address.');
        }

        return $ipAddress;
    }

    /**
     * @param mixed $port
     * @return int
     * @throws InvalidPortException
     */
    public static function getValidPortOf(mixed $port): int
    {
        if (is_int($port) && $port >= 0 && $port <= 65535) {
            return $port;
        }

        throw new InvalidPortException('Please provide a valid port number.');
    }
}
