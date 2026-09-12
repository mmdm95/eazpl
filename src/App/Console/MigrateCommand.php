<?php

namespace Eazpl\App\Console;

use Eazpl\App\Database\Database;
use Eazpl\App\Database\Migration;
use Illuminate\Database\Capsule\Manager as Capsule;

final class MigrateCommand implements ConsoleCommand
{
    public static function name(): string
    {
        return 'migrate';
    }

    public static function description(): string
    {
        return 'Run pending application migrations.';
    }

    public function run(array $arguments): int
    {
        Database::ensureDatabase();
        Database::boot();
        $this->ensureMigrationsTable();

        $files = glob(dirname(__DIR__) . '/Database/Migrations/*.php') ?: [];
        sort($files);
        $executed = Capsule::table('migrations')->pluck('migration')->all();
        $pending = array_diff(array_map('basename', $files), $executed);

        if ($pending === []) {
            $latest = Capsule::table('migrations')
                ->orderByDesc('batch')
                ->orderByDesc('migration')
                ->value('migration');

            echo $latest === null
                ? "Nothing to migrate. No migrations have been recorded yet.\n"
                : "Nothing to migrate. The latest applied migration is: {$latest}\n";
            return 0;
        }

        $batch = ((int) Capsule::table('migrations')->max('batch')) + 1;

        foreach ($files as $file) {
            $migration = basename($file);

            if (!in_array($migration, $pending, true)) {
                continue;
            }

            /** @var Migration $instance */
            $instance = require $file;
            $instance->up();
            Capsule::table('migrations')->insert([
                'migration' => $migration,
                'batch' => $batch,
                'migrated_at' => date('Y-m-d H:i:s'),
            ]);
            echo "Migrated: {$migration}\n";
        }

        return 0;
    }

    private function ensureMigrationsTable(): void
    {
        if (Capsule::schema()->hasTable('migrations')) {
            return;
        }

        Capsule::schema()->create('migrations', function ($table): void {
            $table->id();
            $table->string('migration')->unique();
            $table->unsignedInteger('batch')->index();
            $table->timestamp('migrated_at');
        });
    }
}
