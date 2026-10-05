<?php

namespace App\Models;

class Clothes extends Product
{
    protected static function categoryName(): ?string
    {
        return 'clothes';
    }
}
