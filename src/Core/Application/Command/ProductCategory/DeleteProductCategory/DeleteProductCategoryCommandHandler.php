<?php

declare(strict_types=1);

namespace App\Core\Application\Command\ProductCategory\DeleteProductCategory;

use App\Core\Domain\Model\ProductCategory\ProductCategoryRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class DeleteProductCategoryCommandHandler
{
    public function __construct(
        private ProductCategoryRepositoryInterface $productCategoryRepository,
    ) {
    }

    public function __invoke(DeleteProductCategoryCommand $command): void
    {
        $productCategory = $this->productCategoryRepository->find($command->id);

        // todo delete method on product category

        $this->productCategoryRepository->remove($productCategory);
    }
}