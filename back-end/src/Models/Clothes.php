<?php

declare(strict_types=1);

namespace App\Models;


class Clothes extends Product
{
    
    protected function getCategoryName(): string
    {
        return 'clothes';
    }
}