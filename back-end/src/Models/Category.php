<?php

declare(strict_types=1);

namespace App\Models;

use PDO;
use App\Database\Database;

class Category
{
    
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
    }

    public function findAll()
    {
        $stmt = $this->db->query("SELECT * FROM categories");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

   
    public function findById(string $id)
    {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
