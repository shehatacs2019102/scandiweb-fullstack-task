<?php

namespace App\Models;


class Clothes extends Product
{
   
    protected function getCategoryName(): string
    {
        return 'clothes';
    }
}