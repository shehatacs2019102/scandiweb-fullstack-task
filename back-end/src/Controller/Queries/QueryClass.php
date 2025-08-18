<?php

namespace App\Controller\Queries;

abstract class QueryClass 
{
    public $type;
    public $args;
    public $resolve;

    abstract public function init();
}