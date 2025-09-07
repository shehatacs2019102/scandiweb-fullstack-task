<?php

declare(strict_types=1);

namespace App\Controller\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class OrderType extends TypeClass
{
   
    public function __construct()
    {
        $this->name = 'Order';
        $this->fields = [
            'id' => [
                'type' => Type::nonNull(Type::id()),
                'description' => 'The unique identifier for the order.'
            ],
            'items' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'A JSON-encoded string of items in the order. Consider creating a separate OrderItemType for a more structured API.'
            ],
            'total_price' => [
                'type' => Type::nonNull(Type::float()),
                'description' => 'The total price of the order.'
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