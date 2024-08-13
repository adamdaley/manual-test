<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Repository;

use App\Core\Domain\Model\ProductCategory\ProductCategory;
use App\Core\Domain\Model\ProductCategory\ProductCategoryRepositoryInterface;
use App\Shared\Infrastructure\Repository\AbstractAggregateRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @extends AbstractAggregateRepository<ProductCategory>
 */
class ProductCategoryRepository extends AbstractAggregateRepository implements ProductCategoryRepositoryInterface
{
    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct($entityManager, ProductCategory::class);
    }
}