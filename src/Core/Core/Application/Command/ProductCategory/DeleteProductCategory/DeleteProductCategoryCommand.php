<?php

declare(strict_types=1);

namespace App\Core\Application\Command\ProductCategory\DeleteProductCategory;

use Symfony\Component\Uid\UuidV7;

final readonly class DeleteProductCategoryCommand
{
    public function __construct(
        public UuidV7 $id,
    ) {
    }
}