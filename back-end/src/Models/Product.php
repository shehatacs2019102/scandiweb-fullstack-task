<?php

declare(strict_types=1);

namespace App\Models;

use PDO;
use App\Database\Database;

abstract class Product
{
   
    protected PDO $db;

    
    private const BASE_QUERY = "
        SELECT p.id, p.name, p.description, p.in_stock,
               p.gallery, p.brand,
               COALESCE(pr.amount, 0) as amount,
               COALESCE(pr.currency_label, 'N/A') as currency_label,
               COALESCE(pr.currency_symbol, '-') as currency_symbol,
               p.category_id
        FROM products p
        LEFT JOIN prices pr ON p.id = pr.product_id
        JOIN categories c ON p.category_id = c.id
    ";

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
    }

   
    abstract protected function getCategoryName(): string;

    
    final public function findAll(): array
    {
        $sql = self::BASE_QUERY . " WHERE c.name = :category";
        $stmt = $this->db->prepare($sql);
        
        $stmt->execute([':category' => $this->getCategoryName()]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
    }

   
    final public function findById(string|int $id): array|false
    {
        $sql = self::BASE_QUERY . " WHERE p.id = :id AND c.name = :category";
        $stmt = $this->db->prepare($sql);

        $stmt->execute([
            ':id' => $id,
            ':category' => $this->getCategoryName()
        ]);
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}