<?php

declare(strict_types=1);

namespace App\Tests\Core\Domain\Model\Product;

use App\Core\Domain\Model\ProductCategory\Exception\ProductNotFoundException;
use App\Core\Domain\Model\ProductCategory\Product;
use App\Core\Domain\Model\ProductCategory\ProductCategory;
use Doctrine\Common\Collections\Collection;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV7;

class ProductCategoryTest extends TestCase
{
    use ProductCategoryHelperTrait;

    private ProductCategory $subject;

    public function setUp(): void
    {
        $this->subject = $this->generateProductCategory();
    }

    public function testConstruct_WithEmptyName_ThrowsInvalidArgumentException(): void
    {
        // ASSERT
        $this->expectException(InvalidArgumentException::class);

        // ARRANGE & ACT
        new ProductCategory('');
    }

    public function testConstruct_WithValidData_ReturnsProductCategoryInstance(): void
    {
        // ARRANGE, ACT & ASSERT
        $this->assertInstanceOf(ProductCategory::class, $this->subject);
        $this->assertInstanceOf(UuidV7::class, $this->subject->getId());
        $this->assertSame('Test product category', $this->subject->name);
        $this->assertInstanceOf(Collection::class, $this->subject->getProducts());
        $this->assertEmpty($this->subject->getProducts());
    }

    public function testGetId_WithValidData_ReturnsUuidV7(): void
    {
        // ARRANGE & ACT
        $result = $this->subject->getId();

        // ASSERT
        $this->assertInstanceOf(UuidV7::class, $result);
    }

    public function testGetProductById_WithAnUnknownId_ThrowsProductNotFoundException(): void
    {
        // ARRANGE
        $this->subject->addProduct('Product 1');
        $this->subject->addProduct('Product 2');

        $unknownProductId = Uuid::v7();

        // ASSERT
        $this->expectException(ProductNotFoundException::class);
        $this->expectExceptionMessage(sprintf('Product %s not found.', $unknownProductId));

        // ACT
        $this->subject->getProductById($unknownProductId);
    }

    public function testGetProductById_WithValidData_ReturnsProductInstance(): void
    {
        // ARRANGE
        $product1 = $this->subject->addProduct('Product 1');
        $this->subject->addProduct('Product 2');

        // ACT
        $result = $this->subject->getProductById($product1->getId());

        // ASSERT
        $this->assertSame($product1, $result);
    }

    public function testAddProduct_WithValidData_ReturnsProductInstance(): void
    {
        // ARRANGE & ACT
        $result = $this->subject->addProduct('Product 1');

        // ASSERT
        $this->assertInstanceOf(Product::class, $result);
        $this->assertInstanceOf(UuidV7::class, $result->getId());
        $this->assertSame($this->subject, $result->productCategory);
        $this->assertSame('Product 1', $result->name);
    }

    public function testRemoveProduct_WithUnknownProductId_ThrowsProductNotFoundException(): void
    {
        // ARRANGE
        $this->subject->addProduct('Product 1');

        // ASSERT
        $this->expectException(ProductNotFoundException::class);

        // ACT
        $this->subject->removeProduct(Uuid::v7());
    }

    public function testRemoveProduct_WithValidData_RemovesProductInstance(): void
    {
        // ARRANGE
        $product = $this->subject->addProduct('Product 1');

        // ACT
        $this->subject->removeProduct($product->getId());

        // ASSERT
        $this->assertEmpty($this->subject->getProducts());
    }

    public function testFilterProductsByRestrictionIds_WithEmptyRestrictionIds_ReturnsUnfilteredProductsArray(): void
    {
        // ARRANGE
        $product1 = $this->subject->addProduct('Product 1');
        $product2 = $this->subject->addProduct('Product 2');

        // ACT
        $result = $this->subject->filterProductsByRestrictionIds([]);

        // ASSERT
        $this->assertSame([$product1, $product2], $result->toArray());
    }

    public function testFilterProductsByRestrictionIds_WithRestrictionIds_ReturnsFilteredProductsArray(): void
    {
        // ARRANGE
        $product1 = $this->subject->addProduct('Product 1');
        $product2 = $this->subject->addProduct('Product 2');

        // ACT
        $result = $this->subject->filterProductsByRestrictionIds([$product2->getId()]);

        // ASSERT
        $this->assertSame([$product1], $result->toArray());
    }

    public function testJsonSerialize_WithValidData_ReturnsArray(): void
    {
        // ARRANGE
        $product1 = $this->subject->addProduct('Product 1');
        $product2 = $this->subject->addProduct('Product 2');

        // ACT
        $result = $this->subject->jsonSerialize();

        // ASSERT
        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', $result);
        $this->assertSame($this->subject->getId(), $result['id']);
        $this->assertArrayHasKey('name', $result);
        $this->assertSame('Test product category', $result['name']);
        $this->assertArrayHasKey('products', $result);

        $this->assertIsArray($result['products']);
        $this->assertCount(2, $result['products']);

        $this->assertArrayHasKey('id', $result['products'][0]);
        $this->assertSame($product1->getId(), $result['products'][0]['id']);
        $this->assertArrayHasKey('name', $result['products'][0]);
        $this->assertSame($product1->name, $result['products'][0]['name']);

        $this->assertArrayHasKey('id', $result['products'][1]);
        $this->assertSame($product2->getId(), $result['products'][1]['id']);
        $this->assertArrayHasKey('name', $result['products'][1]);
        $this->assertSame($product2->name, $result['products'][1]['name']);
    }
}