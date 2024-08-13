<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\ProductCategory;

use App\Shared\Infrastructure\Repository\AggregateRepositoryInterface;

/**
 * @extends AggregateRepositoryInterface<ProductCategory>
 */
interface ProductCategoryRepositoryInterface extends AggregateRepositoryInterface
{
}