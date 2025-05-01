<?php

namespace App\Controller\Queries;

use GraphQL\Type\Definition\Type;
use App\Database\Database;

class ProductByIDQuery extends QueryClass 
{
    private static $db;
    public function __construct($typeObject) {
        self::$db = (new Database())->getConnection();
        $this -> type = $typeObject;
        $this -> args = [
            'id' => ['type' => Type::nonNull(Type::id())]
        ];
        $this -> resolve = function ($root, $args) {
            $stmt = self::$db->prepare("
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
            $stmt->execute([':id' => $args['id']]);
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        };
    }
    public function init()
    {
        return [
            'type' => $this -> type,
            'args' => $this -> args,
            'resolve' => $this ->resolve
        ];
    }
}