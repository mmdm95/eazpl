<?php

namespace Eazpl\App\Services;

use InvalidArgumentException;
use RuntimeException;

final class ZplPreviewRenderer
{
    private const LABELARY_URL = 'https://api.labelary.com/v1/printers';

    public function render(string $zpl, array $label): string
    {
        $dpmm = match ((int)($label['dpi'] ?? 0)) {
            203 => 8,
            300 => 12,
            600 => 24,
            default => throw new InvalidArgumentException('The label DPI is not supported by the preview renderer.'),
        };

        $width = ((int)$label['width']) / $dpmm / 25.4;
        $height = ((int)$label['height']) / $dpmm / 25.4;

        if (($label['orientation'] ?? 'portrait') === 'landscape') {
            [$width, $height] = [$height, $width];
        }

        $url = sprintf(
            '%s/%ddpmm/labels/%.3fx%.3f/0/',
            self::LABELARY_URL,
            $dpmm,
            $width,
            $height,
        );
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/x-www-form-urlencoded\r\nAccept: image/png",
                'content' => $zpl,
                'ignore_errors' => true,
                'timeout' => 10,
            ],
            'ssl' => ['verify_peer' => true, 'verify_peer_name' => true],
        ]);

        $response = file_get_contents($url, false, $context);

        if ($response === false) {
            throw new RuntimeException('Unable to connect to the Labelary preview service.');
        }

        $status = $this->responseStatus($http_response_header ?? []);
        if ($status < 200 || $status >= 300) {
            throw new RuntimeException(trim($response) ?: "The Labelary preview service returned status {$status}.");
        }

        $base64 = base64_encode($response);
        if ($base64 === false) {
            throw new RuntimeException('Unable to encode the Labelary preview image.');
        }

        return 'data:image/png;base64,' . $base64;
    }

    private function responseStatus(array $headers): int
    {
        foreach ($headers as $header) {
            if (preg_match('/^HTTP\/\S+\s+(\d{3})/', (string)$header, $matches) === 1) {
                return (int)$matches[1];
            }
        }

        throw new RuntimeException('The Labelary preview service returned an invalid response.');
    }
}
