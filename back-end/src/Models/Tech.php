<?php

declare(strict_types=1);

namespace App\Models;


class Tech extends Product
{
    
    protected function getCategoryName(): string
    {
        return 'tech';
    }
}