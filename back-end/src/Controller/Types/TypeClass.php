<?php

namespace App\Controller\Types;

abstract class TypeClass 
{
    public $name;
    public $fields;

    abstract public function init();
}