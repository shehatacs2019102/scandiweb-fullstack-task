<?php

declare(strict_types=1);

require_once __DIR__ . '/./vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . "/.");
$dotenv->load();


$host = $_ENV['DB_HOST'];
$dbname = $_ENV['DB_NAME'];
$user = $_ENV['DB_USER'];
$pass = $_ENV['DB_PASS'];

try {
    $pdo = new PDO("mysql:host=$host;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname`;USE `$dbname`;");
                
    $queries = [
        "CREATE TABLE IF NOT EXISTS categories (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL
        )",

        "CREATE TABLE IF NOT EXISTS products (
            id VARCHAR(255) PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            description TEXT,
            in_stock BOOLEAN NOT NULL,
            gallery JSON,
            category_id INT,
            brand VARCHAR(255),
            FOREIGN KEY (category_id) REFERENCES categories(id)
        )",

        "CREATE TABLE IF NOT EXISTS attributes (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(255) NOT NULL,
            type VARCHAR(255) NOT NULL
        )",

        "CREATE TABLE IF NOT EXISTS attribute_values (
            id INT AUTO_INCREMENT PRIMARY KEY,
            attribute_id INT,
            display_value VARCHAR(255),
            value VARCHAR(255),
            FOREIGN KEY (attribute_id) REFERENCES attributes(id)
        )",
        
        "CREATE TABLE IF NOT EXISTS product_attributes (
            product_id VARCHAR(255),
            attribute_value_id INT,
            FOREIGN KEY (product_id) REFERENCES products(id),
            FOREIGN KEY (attribute_value_id) REFERENCES attribute_values(id)
        )",

        "CREATE TABLE IF NOT EXISTS prices (
            id INT AUTO_INCREMENT PRIMARY KEY,
            product_id VARCHAR(255),
            amount DECIMAL(10, 2) NOT NULL,
            currency_label VARCHAR(10),
            currency_symbol VARCHAR(5),
            FOREIGN KEY (product_id) REFERENCES products(id)
        )",

        "CREATE TABLE IF NOT EXISTS orders (
            id INT PRIMARY KEY AUTO_INCREMENT,
            items VARCHAR(8000),
            total_price DECIMAL(10,2) NOT NULL
        );"
    ];
    
    foreach ($queries as $query) {
        $pdo->exec($query);
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}