<?php

namespace App\Models;

use PDO;
use App\Database\Database;

class Product
{
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
    }

    public function findById($id)
    {
        $stmt = $this->db->prepare("
            SELECT p.id, p.name, p.description, p.in_stock, 
                   p.gallery, p.brand, 
                   COALESCE(pr.amount, 0) as amount, 
                   COALESCE(pr.currency_label, 'N/A') as currency_label, 
                   COALESCE(pr.currency_symbol, '-') as currency_symbol,
                   p.category_id
            FROM products p
            LEFT JOIN prices pr ON p.id = pr.product_id
            WHERE p.id = :id
        ");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findAll()
{
    $stmt = $this->db->query("
        SELECT p.id, p.name, p.description, p.in_stock, p.gallery, p.brand, 
               COALESCE(pr.amount, 0) as amount, 
               COALESCE(pr.currency_label, 'N/A') as currency_label, 
               COALESCE(pr.currency_symbol, '-') as currency_symbol, 
               p.category_id
        FROM products p
        LEFT JOIN prices pr ON p.id = pr.product_id
    ");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}
