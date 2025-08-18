<?php

namespace App\Controller\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class CategoryType extends TypeClass 
{
    public function __construct() {
        $this -> name = 'ProductCategory';
        $this -> fields = [
        'name' => Type::nonNull(Type::string())
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