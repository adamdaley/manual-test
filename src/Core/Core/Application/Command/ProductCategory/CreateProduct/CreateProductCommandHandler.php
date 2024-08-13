<?php

declare(strict_types=1);

namespace App\Core\Application\Command\ProductCategory\CreateProduct;

use App\Core\Domain\Model\ProductCategory\Product;
use App\Core\Domain\Model\ProductCategory\ProductCategoryRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class CreateProductCommandHandler
{
    public function __construct(
        private ProductCategoryRepositoryInterface $productCategoryRepository,
    ) {
    }

    public function __invoke(CreateProductCommand $command): Product
    {
        $productCategory = $this->productCategoryRepository->find($command->productCategoryId);

        return $productCategory->addProduct($command->name);
    }
}