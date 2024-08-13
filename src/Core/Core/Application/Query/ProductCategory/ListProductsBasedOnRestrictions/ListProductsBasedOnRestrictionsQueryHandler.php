<?php

declare(strict_types=1);

namespace App\Core\Application\Query\ProductCategory\ListProductsBasedOnRestrictions;

use App\Core\Domain\Model\ProductCategory\Product;
use App\Core\Domain\Model\ProductCategory\ProductCategoryRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class ListProductsBasedOnRestrictionsQueryHandler
{
    public function __construct(
        private ProductCategoryRepositoryInterface $productCategoryRepository,
    ) {
    }

    /**
     * @return list<Product>
     */
    public function __invoke(ListProductsBasedOnRestrictionsQuery $command): array
    {
        $productCategory = $this->productCategoryRepository->find($command->productCategoryId);

        // todo should probably do this in the domain
        return array_values($productCategory->filterProductsByRestrictionIds($command->productRestrictionIds)->toArray());
    }
}