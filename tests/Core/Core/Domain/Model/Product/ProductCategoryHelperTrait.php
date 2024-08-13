<?php

declare(strict_types=1);

namespace App\Tests\Core\Domain\Model\Product;

use App\Core\Domain\Model\ProductCategory\Product;
use App\Core\Domain\Model\ProductCategory\ProductCategory;

trait ProductCategoryHelperTrait
{
    public function generateProductCategory(): ProductCategory
    {
        return new ProductCategory('Test product category');
    }

    public function generateProduct(?ProductCategory $productCategory = null): Product
    {
        if (!$productCategory instanceof ProductCategory) {
            $productCategory = $this->generateProductCategory();
        }

        return new Product($productCategory, 'Test product');
    }
}