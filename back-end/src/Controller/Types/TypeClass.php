<?php

declare(strict_types=1);

namespace App\Controller\Types;

use GraphQL\Type\Definition\ObjectType;


abstract class TypeClass
{
   
    public string $name;

    
    public array $fields;

    
    abstract public function init(): ObjectType;
}