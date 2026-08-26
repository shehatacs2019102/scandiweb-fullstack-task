<?php

declare(strict_types=1);

namespace App\Database;

use PDO;
use PDOException;

class Database
{

    private ?PDO $pdo = null;

    public static function buildDsn(?string $dbname = null): string
    {
        $host = $_ENV['DB_HOST'];
        $port = $_ENV['DB_PORT'] ?? null;

        $dsn = "mysql:host={$host}";
        if ($port) {
            $dsn .= ";port={$port}";
        }
        if ($dbname) {
            $dsn .= ";dbname={$dbname}";
        }
        $dsn .= ";charset=utf8";

        return $dsn;
    }

    public static function pdoOptions(): array
    {
        $sslCa = $_ENV['DB_SSL_CA'] ?? null;

        return $sslCa ? [PDO::MYSQL_ATTR_SSL_CA => $sslCa] : [];
    }

    public function __construct()
    {
        $dbname = $_ENV['DB_NAME'];
        $user = $_ENV['DB_USER'];
        $pass = $_ENV['DB_PASS'];

        try {
            $this->pdo = new PDO(self::buildDsn($dbname), $user, $pass, self::pdoOptions());
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    public function getConnection(): ?PDO
    {
        return $this->pdo;
    }

}