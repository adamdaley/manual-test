<?php

declare(strict_types=1);

namespace App\Core\Application\Query\ProductCategory\GetProductCategory;

use Symfony\Component\Uid\UuidV7;

final readonly class GetProductCategoryQuery
{
    public function __construct(
        public UuidV7 $id,
    ) {
    }
}