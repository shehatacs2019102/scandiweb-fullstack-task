<?php

namespace App\GraphQL\Queries;

use App\Models\Product;

final class GetProductById
{
    /**
     * @param  array{id: string}  $args
     */
    public function __invoke(mixed $root, array $args): ?Product
    {
        return Product::with(GetProducts::RELATIONS)->find($args['id']);
    }
}
