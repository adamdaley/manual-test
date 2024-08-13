<?php

declare(strict_types=1);

namespace App\Core\Application\Query\ProductCategory\GetProduct;

use App\Core\Domain\Model\ProductCategory\Product;
use App\Core\Domain\Model\ProductCategory\ProductCategoryRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GetProductQueryHandler
{
    public function __construct(
        private ProductCategoryRepositoryInterface $productCategoryRepository,
    ) {
    }

    public function __invoke(GetProductQuery $command): Product
    {
        $productCategory = $this->productCategoryRepository->find($command->productCategoryId);

        return $productCategory->getProductById($command->productId);
    }
}