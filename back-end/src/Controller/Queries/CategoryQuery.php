<?php

namespace App\Controller\Queries;

use GraphQL\Type\Definition\Type;
use App\Models\Category;

class CategoryQuery extends QueryClass 
{
    public function __construct($typeObject)
    {
        $categoryModel = new Category();

        $this->type = Type::listOf($typeObject);
        $this->resolve = function () use ($categoryModel) {
            return $categoryModel->findAll();
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
