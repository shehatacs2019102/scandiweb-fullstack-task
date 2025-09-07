<?php

declare(strict_types=1);

namespace App\Database;

use PDO;
use PDOException;

/**
 * Handles the database connection using PDO.
 */
class Database
{
    /**
     * The PDO instance.
     *
     * @var PDO|null
     */
    private ?PDO $pdo = null;

    /**
     * Class constructor.
     *
     * Establishes a new database connection.
     * It's generally better to throw an exception on failure
     * than to echo an error message.
     */
    public function __construct()
    {
        $host = $_ENV['DB_HOST'];
        $dbname = $_ENV['DB_NAME'];
        $user = $_ENV['DB_USER'];
        $pass = $_ENV['DB_PASS'];
        $dsn = "mysql:host={$host};dbname={$dbname};charset=utf8";

        try {
            $this->pdo = new PDO($dsn, $user, $pass);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            // Re-throwing the exception allows the calling code to handle the error gracefully.
            throw new PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    /**
     * Returns the active PDO connection instance.
     *
     * @return PDO|null
     */
    public function getConnection(): ?PDO
    {
        return $this->pdo;
    }

    /**
     * A placeholder method for any database setup logic,
     * such as creating tables or seeding initial data.
     *
     * @return void
     */
    public function setup(): void
    {
        // Intentionally empty. Implement setup logic here.
    }
}