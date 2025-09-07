<?php

declare(strict_types=1);

namespace App\Controller\Queries;

use GraphQL\Type\Definition\Type;
use App\Models\Tech;
use App\Models\Clothes;

class ProductByIDQuery extends QueryClass
{
    /**
     * @param mixed $productType
     */
    public function __construct($productType)
    {
        $techModel = new Tech();
        $clothesModel = new Clothes();

        $this->type = $productType;

        $this->args = [
            'id' => ['type' => Type::nonNull(Type::id())]
        ];

        $this->resolve = function ($root, $args) use ($techModel, $clothesModel) {
            $id = $args['id'];

            $product = $techModel->findById($id);
            if ($product) {
                return $product;
            }

            $product = $clothesModel->findById($id);
            if ($product) {
                return $product;
            }

            return null;
        };
    }


    public function init(): array
    {
        return [
            'type' => $this->type,
            'args' => $this->args,
            'resolve' => $this->resolve
        ];
    }
}