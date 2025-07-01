<?php

namespace App\Controller\Queries;

use GraphQL\Type\Definition\Type;
use App\Database\Database;
use App\Models\Product;

class ProductByIDQuery extends QueryClass 
{
   
    public function __construct($typeObject) {
        $productModel = new Product();
        
        $this -> type = $typeObject;
        $this -> args = [
            'id' => ['type' => Type::nonNull(Type::id())]
        ];
        $this->resolve = function ($root, $args) use ($productModel) {
            return $productModel->findById($args['id']);
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