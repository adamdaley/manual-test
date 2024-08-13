<?php

declare(strict_types=1);

namespace App\Core\Application\Query\ProductCategory\ListProducts;

use App\Core\Domain\Model\ProductCategory\Product;
use App\Core\Domain\Model\ProductCategory\ProductCategoryRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class ListProductsQueryHandler
{
    public function __construct(
        private ProductCategoryRepositoryInterface $productCategoryRepository,
    ) {
    }

    /**
     * @return list<Product>
     */
    public function __invoke(ListProductsQuery $command): array
    {
        $productCategory = $this->productCategoryRepository->find($command->productCategoryId);

        return $productCategory->getProducts()->toArray();
    }
}