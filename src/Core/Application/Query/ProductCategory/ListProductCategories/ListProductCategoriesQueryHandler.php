<?php

declare(strict_types=1);

namespace App\Core\Application\Query\ProductCategory\ListProductCategories;

use App\Core\Domain\Model\ProductCategory\ProductCategory;
use App\Core\Domain\Model\ProductCategory\ProductCategoryRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class ListProductCategoriesQueryHandler
{
    public function __construct(
        private ProductCategoryRepositoryInterface $productCategoryRepository,
    ) {
    }

    /**
     * @return list<ProductCategory>
     */
    public function __invoke(ListProductCategoriesQuery $query): array
    {
        return $this->productCategoryRepository->findAll();
    }
}