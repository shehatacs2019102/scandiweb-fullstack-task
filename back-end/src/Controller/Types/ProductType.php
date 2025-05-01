<?php

namespace App\Controller\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use App\Database\Database;

class ProductType extends TypeClass 
{
    private static $db;
    public function __construct($productAttributeType, $CategoryType) {
        self::$db = (new Database())->getConnection();
        $this -> name = 'Product';
        $this -> fields = [
            'id' => Type::nonNull(Type::id()),
            'name' => Type::nonNull(Type::string()),
            'description' => Type::string(),
            'gallery' => Type::string(),
            'amount' => Type::float(),
            'currency_label' => Type::string(),
            'currency_symbol' => Type::string(),
            'in_stock' => Type::boolean(),
            'brand' => Type::string(),
            'attributes' => [
                'type' => Type::listOf($productAttributeType),
                'resolve' => function ($product) {
                    $stmt = self::$db->prepare("
                       SELECT a.id, a.name, av.value 
                       FROM product_attributes pa
                       JOIN attribute_values av ON pa.attribute_value_id = av.id
                       JOIN attributes a ON av.attribute_id = a.id
                       WHERE pa.product_id = :product_id
                    ");
                    $stmt->execute([':product_id' => $product['id']]);
                    return $stmt->fetchAll(\PDO::FETCH_ASSOC);
                }
            ],
            'category' => [
                'type' => Type::nonNull($CategoryType ),
                'resolve' => function ($product) {
                    $stmt = self::$db->prepare("SELECT name FROM categories WHERE id = :id");
                    $stmt->execute([':id' => $product['category_id']]);
                    return $stmt->fetch(\PDO::FETCH_ASSOC);
                }
            ],
        ];
    }
    public function init(): ObjectType
    {
        return new ObjectType([
            'name' => $this->name,
            'fields' => $this->fields,
        ]);
    }
}