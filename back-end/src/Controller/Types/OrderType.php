<?php

namespace App\Controller\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class OrderType extends TypeClass 
{
    public function __construct() {
        $this -> name = 'Order';
        $this -> fields = [
            'id' => ['type' => Type::nonNull(Type::int())],
            'items' => Type::nonNull(Type::string()),
            'total_price' => ['type' => Type::nonNull(Type::float())],
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