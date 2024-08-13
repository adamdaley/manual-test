<?php

declare(strict_types=1);

namespace App\Core\Application\Command\ProductCategory\CreateProductCategory;

use App\Core\Domain\Model\ProductCategory\ProductCategory;
use App\Core\Domain\Model\ProductCategory\ProductCategoryRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class CreateProductCategoryCommandHandler
{
    public function __construct(
        private ProductCategoryRepositoryInterface $productCategoryRepository,
    ) {
    }

    public function __invoke(CreateProductCategoryCommand $command): ProductCategory
    {
        $productCategory = new ProductCategory($command->name);

        $this->productCategoryRepository->add($productCategory);

        return $productCategory;
    }
}