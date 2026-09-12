<?php

namespace Eazpl\App\Console;

use Eazpl\App\Database\Database;
use Eazpl\App\Database\Migration;
use Illuminate\Database\Capsule\Manager as Capsule;
use RuntimeException;

final class MigrateResetCommand implements ConsoleCommand
{
    public static function name(): string
    {
        return 'migrate:reset';
    }

    public static function description(): string
    {
        return 'Roll back all application migrations.';
    }

    public function run(array $arguments): int
    {
        Database::ensureDatabase();
        Database::boot();

        if (!Capsule::schema()->hasTable('migrations')) {
            echo "Nothing to reset.\n";
            return 0;
        }

        $migrations = Capsule::table('migrations')
            ->orderByDesc('batch')
            ->orderByDesc('migration')
            ->get(['migration']);

        foreach ($migrations as $record) {
            $name = (string)$record->migration;
            $file = dirname(__DIR__) . '/Database/Migrations/' . $name;

            if (!is_file($file)) {
                throw new RuntimeException("Migration file is missing: {$name}.");
            }

            /** @var Migration $instance */
            $instance = require $file;
            $instance->down();
            Capsule::table('migrations')->where('migration', $name)->delete();
            echo "Rolled back: {$name}\n";
        }

        return 0;
    }
}
