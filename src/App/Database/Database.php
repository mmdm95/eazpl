<?php

namespace Eazpl\App\Database;

use Illuminate\Database\Capsule\Manager as Capsule;
use PDO;
use RuntimeException;

final class Database
{
    private static ?Capsule $capsule = null;

    public static function boot(): Capsule
    {
        if (self::$capsule instanceof Capsule) {
            return self::$capsule;
        }

        $configuration = self::configuration();
        $capsule = new Capsule();
        $capsule->addConnection($configuration);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        return self::$capsule = $capsule;
    }

    public static function ensureDatabase(): void
    {
        $configuration = self::configuration();

        if ($configuration['driver'] !== 'mysql') {
            return;
        }

        $database = (string)$configuration['database'];

        if (!preg_match('/^[A-Za-z0-9_]+$/', $database)) {
            throw new RuntimeException('The configured database name is invalid.');
        }

        $dsn = sprintf(
            'mysql:host=%s;port=%s;charset=%s',
            $configuration['host'],
            $configuration['port'],
            $configuration['charset'],
        );
        $connection = new PDO(
            $dsn,
            (string)$configuration['username'],
            (string)$configuration['password'],
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION],
        );
        $connection->exec(
            "CREATE DATABASE IF NOT EXISTS `{$database}` CHARACTER SET {$configuration['charset']} COLLATE {$configuration['collation']}",
        );
    }

    private static function configuration(): array
    {
        return [
            'driver' => getenv('EAZPL_DB_DRIVER') ?: 'mysql',
            'host' => getenv('EAZPL_DB_HOST') ?: '127.0.0.1',
            'port' => getenv('EAZPL_DB_PORT') ?: '3306',
            'database' => getenv('EAZPL_DB_DATABASE') ?: 'eazpl',
            'username' => getenv('EAZPL_DB_USERNAME') ?: 'root',
            'password' => getenv('EAZPL_DB_PASSWORD') ?: '',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
        ];
    }
}
