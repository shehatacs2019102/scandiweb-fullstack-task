<?php

namespace App\Controller\Queries;

use GraphQL\Type\Definition\Type;
use App\Database\Database;

class AllProductsQuery extends QueryClass 
{
   private static $db;
    public function __construct($typeObject) {
        self::$db = (new Database())->getConnection();
        $this -> type = Type::listOf($typeObject);
        $this -> resolve = function () {
            $stmt = self::$db->query("
                SELECT p.id, p.name, p.description, p.in_stock, p.gallery, p.brand, 
                       pr.amount, pr.currency_label, pr.currency_symbol, 
                       p.category_id
                FROM products p
                LEFT JOIN prices pr ON p.id = pr.product_id
            ");
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        };
    }
    public function init()
    {
        return [
            'type' => $this -> type,
            'resolve' => $this ->resolve
        ];
    }
}