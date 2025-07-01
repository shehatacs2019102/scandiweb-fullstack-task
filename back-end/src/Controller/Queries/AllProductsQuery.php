<?php

namespace App\Controller\Queries;

use GraphQL\Type\Definition\Type;

use App\Models\Product;

class AllProductsQuery extends QueryClass 
{
    private static $db;
    public function __construct($typeObject) {
        $productModel = new Product();
        $this->type = Type::listOf($typeObject);
        $this->resolve = function () use ($productModel) {
            return $productModel->findAll();
        };
        
    }

    public function init()
    {
        return [
            'type' => $this->type,
            'resolve' => $this->resolve
        ];
    }
}
