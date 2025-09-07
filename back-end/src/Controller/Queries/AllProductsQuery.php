<?php

declare(strict_types=1);

namespace App\Controller\Queries;

use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ObjectType;
use App\Models\Tech;
use App\Models\Clothes;

class AllProductsQuery extends QueryClass
{
    
    public function __construct($typeObject)
    {
        $classifiedProductsType = new ObjectType([
            'name' => 'ClassifiedProducts',
            'description' => 'A collection of products classified by category.',
            'fields' => [
                'tech' => [
                    'type' => Type::listOf($typeObject),
                    'description' => 'A list of products in the tech category.'
                ],
                'clothes' => [
                    'type' => Type::listOf($typeObject),
                    'description' => 'A list of products in the clothes category.'
                ]
            ]
        ]);

        $this->type = $classifiedProductsType;
        $this->args = [];

        $this->resolve = function ($root, $args) {
            $techModel = new Tech();
            $clothesModel = new Clothes();

            $techProducts = $techModel->findAll();
            $clothesProducts = $clothesModel->findAll();

            return [
                'tech' => $techProducts,
                'clothes' => $clothesProducts,
            ];
        };
    }

    
    public function init():array
    {
        return [
            'type' => $this->type,
            'args' => $this->args,
            'resolve' => $this->resolve
        ];
    }
}