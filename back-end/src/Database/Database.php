<?php
namespace App\Database;

use PDO;
use PDOException;

class Database {
    private $pdo;

    public function __construct() {
        try {
            
            $this->pdo = new PDO('mysql:host=localhost;dbname=shop', 'root', 'password123');
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
        }
    }

    public function getConnection() {
        return $this->pdo;
    }
}
