<?php

declare(strict_types=1);

namespace App\Core\Application\Command\ProductCategory\DeleteProduct;

use Symfony\Component\Uid\UuidV7;

final readonly class DeleteProductCommand
{
    public function __construct(
        public UuidV7 $productCategoryId,
        public UuidV7 $productId,
    ) {
    }
}