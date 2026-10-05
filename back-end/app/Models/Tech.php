<?php

namespace App\Models;

class Tech extends Product
{
    protected static function categoryName(): ?string
    {
        return 'tech';
    }
}
