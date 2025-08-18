<?php

namespace App\Models;

use PDO;
use App\Database\Database;

class Tech
{
   
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
    }

    
    public function findAll()
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
            JOIN categories c ON p.category_id = c.id
            WHERE c.name = :category
        ");
        $stmt->execute([':category' => 'tech']);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
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
            JOIN categories c ON p.category_id = c.id
            WHERE p.id = :id AND c.name = :category
        ");
        $stmt->execute([':id' => $id, ':category' => 'tech']);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}