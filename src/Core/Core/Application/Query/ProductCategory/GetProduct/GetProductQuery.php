<?php

declare(strict_types=1);

namespace App\Core\Application\Query\ProductCategory\GetProduct;

use Symfony\Component\Uid\UuidV7;

final readonly class GetProductQuery
{
    public function __construct(
        public UuidV7 $productCategoryId,
        public UuidV7 $productId,
    ) {
    }
}