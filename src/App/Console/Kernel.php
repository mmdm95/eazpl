<?php

namespace Eazpl\App\Console;

final class Kernel
{
    /** @var array<string, class-string<ConsoleCommand>> */
    private const COMMANDS = [
        MigrateCommand::class,
        MigrateResetCommand::class,
        MigrateStatusCommand::class,
        ServeCommand::class,
    ];

    /** @param list<string> $arguments */
    public function run(array $arguments): int
    {
        $name = $arguments[0] ?? 'help';

        if ($name === 'help' || $name === '--help') {
            $this->printHelp();
            return 0;
        }

        foreach (self::COMMANDS as $commandClass) {
            if ($commandClass::name() === $name) {
                return (new $commandClass())->run(array_slice($arguments, 1));
            }
        }

        echo "Unknown command: {$name}\n\n";
        $this->printHelp();
        return 1;
    }

    private function printHelp(): void
    {
        echo "eaZPL application commands\n\nUsage:\n  php bin/eazpl <command> [options]\n\nCommands:\n";

        foreach (self::COMMANDS as $commandClass) {
            echo sprintf('  %-16s %s', $commandClass::name(), $commandClass::description()) . "\n";
        }
    }
}
