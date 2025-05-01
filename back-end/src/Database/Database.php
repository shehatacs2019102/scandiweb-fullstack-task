<?php
namespace App\Database;

use PDO;
use PDOException;


class Database {
    private $pdo;
           
    
    public function __construct() {
        $host = $_ENV['DB_HOST'];
        $dbname = $_ENV['DB_NAME'];  
        $user = $_ENV['DB_USER'];    
        $pass = $_ENV['DB_PASS'];
        
        try {
            
            $this->pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo 'Connection failed: ' . $e->getMessage();
        }
    }

    public function getConnection() {
        return $this->pdo;
    }

    public function setup(){
        
    }
}