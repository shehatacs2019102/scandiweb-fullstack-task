<?php

declare(strict_types=1);

namespace App\Controller\Types;

use App\Database\Database;
use App\Models\Attribute;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use PDO;

class ProductType extends TypeClass
{
   
    public function __construct(ObjectType $productAttributeType, ObjectType $categoryType)
    {
        $attributeModel = new Attribute();
        $db = (new Database())->getConnection();

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
                'resolve' => static function (array $product) use ($attributeModel) {
                    return $attributeModel->getAttributesByProductId($product['id']);
                },
            ],
            'category' => [
                'type' => Type::nonNull($categoryType),
                'resolve' => static function (array $product) use ($db) {
                    $stmt = $db->prepare("SELECT name FROM categories WHERE id = :id");
                    $stmt->execute([':id' => $product['category_id']]);
                    return $stmt->fetch(PDO::FETCH_ASSOC);
                },
            ],
        ];
    }

    /**
     * Initializes and returns the Product ObjectType.
     */
    public function init(): ObjectType
    {
        return new ObjectType([
            'name' => $this->name,
            'fields' => $this->fields,
        ]);
    }
}
