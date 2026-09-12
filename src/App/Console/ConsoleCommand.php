<?php

namespace Eazpl\App\Console;

interface ConsoleCommand
{
    public static function name(): string;

    public static function description(): string;

    /** @param list<string> $arguments */
    public function run(array $arguments): int;
}
