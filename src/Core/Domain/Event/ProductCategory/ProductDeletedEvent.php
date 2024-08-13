<?php

declare(strict_types=1);

namespace App\Core\Domain\Event\ProductCategory;

use App\Shared\Domain\Event\DomainEventInterface;
use Symfony\Component\Uid\UuidV7;

final readonly class ProductDeletedEvent implements DomainEventInterface
{
    public function __construct(
        public UuidV7 $id,
        public UuidV7 $productCategoryId,
    ) {
    }
}