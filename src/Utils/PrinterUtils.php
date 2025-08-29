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

        // Check if the IP is a valid IPv4 address
        if (false === filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            throw new InvalidIPAddressException('Please provide a valid IPv4 address.');
        }

        // Check if the IP is a valid IPv6 address
        if (false === filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            throw new InvalidIPAddressException('Please provide a valid IPv6 address.');
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
