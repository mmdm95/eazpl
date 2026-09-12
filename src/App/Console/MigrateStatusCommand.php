<?php

namespace Eazpl\App\Console;

use Eazpl\App\Database\Database;
use Illuminate\Database\Capsule\Manager as Capsule;

final class MigrateStatusCommand implements ConsoleCommand
{
    public static function name(): string
    {
        return 'migrate:status';
    }

    public static function description(): string
    {
        return 'Show applied and pending application migrations.';
    }

    public function run(array $arguments): int
    {
        Database::ensureDatabase();
        Database::boot();

        $files = glob(dirname(__DIR__) . '/Database/Migrations/*.php') ?: [];
        sort($files);
        $applied = Capsule::schema()->hasTable('migrations')
            ? Capsule::table('migrations')->get(['migration', 'batch', 'migrated_at'])
            : [];

        if ($files === []) {
            echo "No migration files found.\n";
            return 0;
        }

        echo "Application migrations\n\n";

        foreach ($files as $file) {
            $name = basename($file);
            $record = null;

            foreach ($applied as $migration) {
                if ($migration->migration === $name) {
                    $record = $migration;
                    break;
                }
            }

            if ($record === null) {
                echo "  [Pending]    {$name}\n";
                continue;
            }

            echo sprintf(
                "  [Applied]    %s (batch %d, %s)\n",
                $name,
                (int)$record->batch,
                (string)$record->migrated_at,
            );
        }

        return 0;
    }
}
