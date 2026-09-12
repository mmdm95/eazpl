<?php

namespace Eazpl\App\Console;

final class ServeCommand implements ConsoleCommand
{
    public static function name(): string
    {
        return 'serve';
    }

    public static function description(): string
    {
        return 'Start the PHP development API server.';
    }

    public function run(array $arguments): int
    {
        $host = $this->option($arguments, 'host', '127.0.0.1');
        $port = $this->option($arguments, 'port', '8000');
        $documentRoot = dirname(__DIR__, 3) . '/public';
        $command = sprintf(
            '%s -S %s:%d -t %s',
            escapeshellarg(PHP_BINARY),
            escapeshellarg($host),
            (int)$port,
            escapeshellarg($documentRoot),
        );

        echo "eaZPL API server running at http://{$host}:{$port}\n";
        passthru($command, $code);

        return $code;
    }

    /** @param list<string> $arguments */
    private function option(array $arguments, string $name, string $default): string
    {
        foreach ($arguments as $argument) {
            if (str_starts_with($argument, "--{$name}=")) {
                return substr($argument, strlen("--{$name}="));
            }
        }

        return $default;
    }
}
