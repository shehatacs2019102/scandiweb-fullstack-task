<?php

namespace App\Controller\Queries;

use GraphQL\Type\Definition\Type;
use App\Database\Database;

class CategoryQuery extends QueryClass 
{
    private static $db;
    public function __construct($typeObject)
    {
        self::$db = (new Database())->getConnection();
        $this -> type = Type::listOf($typeObject);
        $this -> resolve = function () {
            $stmt = self::$db->query("SELECT * FROM categories");
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