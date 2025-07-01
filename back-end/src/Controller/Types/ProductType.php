<?php

namespace App\Controller\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use App\Models\Attribute;
use App\Database\Database;

class ProductType extends TypeClass 
{
    public function __construct($productAttributeType, $CategoryType) {
        $attributeModel = new Attribute();
        $db = (new Database())->getConnection(); // For category only (optional to move to model too)

        $this->name = 'Product';
        $this->fields = [
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
                'resolve' => function ($product) use ($attributeModel) {
                    return $attributeModel->getAttributesByProductId($product['id']);
                }
            ],

            'category' => [
                'type' => Type::nonNull($CategoryType),
                'resolve' => function ($product) use ($db) {
                    $stmt = $db->prepare("SELECT name FROM categories WHERE id = :id");
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
