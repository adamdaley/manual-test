<?php

declare(strict_types=1);

namespace App\Core\Application\Command\ProductCategory\DeleteProduct;

use App\Core\Domain\Model\ProductCategory\ProductCategoryRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class DeleteProductCommandHandler
{
    public function __construct(
        private ProductCategoryRepositoryInterface $productCategoryRepository,
    ) {
    }

    public function __invoke(DeleteProductCommand $command): void
    {
        $productCategory = $this->productCategoryRepository->find($command->productCategoryId);

        $productCategory->removeProduct($command->productId);
    }
}