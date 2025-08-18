<?php

namespace App\Models;

use PDO;
use App\Database\Database;

class Attribute
{
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
    }

    public function getAttributesByProductId($productId)
    {
        $stmt = $this->db->prepare("
            SELECT a.id, a.name, av.value 
            FROM product_attributes pa
            JOIN attribute_values av ON pa.attribute_value_id = av.id
            JOIN attributes a ON av.attribute_id = a.id
            WHERE pa.product_id = :product_id
        ");
        $stmt->execute([':product_id' => $productId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
