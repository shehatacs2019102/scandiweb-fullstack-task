<?php

namespace App\Controller\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class AttributeType extends TypeClass 
{
    public function __construct() {
        $this -> name = 'ProductAttribute';
        $this -> fields = [
        'id' => Type::nonNull(Type::id()),
                    'name' => Type::nonNull(Type::string()),
                    'value' => Type::string(),
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