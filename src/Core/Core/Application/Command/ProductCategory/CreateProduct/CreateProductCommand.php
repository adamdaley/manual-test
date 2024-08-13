<?php

declare(strict_types=1);

namespace App\Core\Application\Command\ProductCategory\CreateProduct;

use Symfony\Component\Uid\UuidV7;

final readonly class CreateProductCommand
{
    public function __construct(
        public UuidV7 $productCategoryId,
        public string $name,
    ) {
    }
}