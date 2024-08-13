<?php

declare(strict_types=1);

namespace App\Core\Application\Query\ProductCategory\ListProducts;

use Symfony\Component\Uid\UuidV7;

final readonly class ListProductsQuery
{
    public function __construct(
        public UuidV7 $productCategoryId,
    ) {
    }
}