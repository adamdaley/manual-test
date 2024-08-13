<?php

declare(strict_types=1);

namespace App\Core\Application\Query\ProductCategory\ListProductsBasedOnRestrictions;

use Symfony\Component\Uid\UuidV7;

final readonly class ListProductsBasedOnRestrictionsQuery
{
    /**
     * @param list<UuidV7> $productRestrictionIds
     */
    public function __construct(
        public UuidV7 $productCategoryId,
        public array $productRestrictionIds,
    ) {
    }
}