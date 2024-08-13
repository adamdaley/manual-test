<?php

declare(strict_types=1);

namespace App\Core\Application\Query\ProductCategory\GetProductCategory;

use App\Core\Domain\Model\ProductCategory\ProductCategory;
use App\Core\Domain\Model\ProductCategory\ProductCategoryRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GetProductCategoryQueryHandler
{
    public function __construct(
        private ProductCategoryRepositoryInterface $productCategoryRepository,
    ) {
    }

    public function __invoke(GetProductCategoryQuery $query): ProductCategory
    {
        return $this->productCategoryRepository->find($query->id);
    }
}