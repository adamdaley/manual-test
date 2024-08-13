<?php

declare(strict_types=1);

namespace App\Core\Application\Command\ProductCategory\CreateProductCategory;

final readonly class CreateProductCategoryCommand
{
    public function __construct(
        public string $name,
    ) {
    }
}