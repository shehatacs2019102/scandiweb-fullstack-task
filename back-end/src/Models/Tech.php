<?php

namespace App\Models;


class Tech extends Product
{
    
    protected function getCategoryName(): string
    {
        return 'tech';
    }
}