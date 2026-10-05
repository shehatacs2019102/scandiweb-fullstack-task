<?php

namespace App\GraphQL\Queries;

use App\Models\Clothes;
use App\Models\Tech;

final class GetProducts
{
    public const RELATIONS = ['price', 'category', 'attributeValues.attribute'];

    /**
     * @return array{tech: \Illuminate\Support\Collection, clothes: \Illuminate\Support\Collection}
     */
    public function __invoke(): array
    {
        return [
            'tech' => Tech::with(self::RELATIONS)->get(),
            'clothes' => Clothes::with(self::RELATIONS)->get(),
        ];
    }
}
