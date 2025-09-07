<?php

declare(strict_types=1);

namespace App\Controller\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;


class AttributeType extends TypeClass
{
   
    public function __construct()
    {
        $this->name = 'ProductAttribute';
        $this->fields = [
            'id' => [
                'type' => Type::nonNull(Type::id()),
                'description' => 'The unique identifier for the attribute.',
            ],
            'name' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'The name of the attribute (e.g., "Size").',
            ],
            'value' => [
                'type' => Type::string(),
                'description' => 'The value of the attribute (e.g., "Large").',
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