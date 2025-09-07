<?php

declare(strict_types=1);

namespace App\Controller\Types;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class CategoryType extends TypeClass
{
    
    public function __construct()
    {
        $this->name = 'ProductCategory';
        $this->fields = [
            'name' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'The name of the category.',
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