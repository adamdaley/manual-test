<?php

declare(strict_types=1);

namespace App\Tests\Core\Domain\Model\Product;

use App\Core\Domain\Model\ProductCategory\Product;
use App\Core\Domain\Model\ProductCategory\ProductCategory;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Uid\UuidV7;

class ProductTest extends TestCase
{
    use ProductCategoryHelperTrait;

    private ProductCategory $productCategory;
    private Product $subject;

    public function setUp(): void
    {
        $this->productCategory = $this->generateProductCategory();

        $this->subject = new Product($this->productCategory, 'Test product');
    }

    public function testConstruct_WithEmptyName_ThrowsInvalidArgumentException(): void
    {
        // ASSERT
        $this->expectException(InvalidArgumentException::class);

        // ARRANGE & ACT
        new Product($this->productCategory, '');
    }

    public function testConstruct_WithValidData_ReturnsProductInstance(): void
    {
        // ARRANGE, ACT & ASSERT
        $this->assertInstanceOf(Product::class, $this->subject);
        $this->assertInstanceOf(UuidV7::class, $this->subject->getId());
        $this->assertSame($this->productCategory, $this->subject->productCategory);
        $this->assertSame('Test product', $this->subject->name);
    }

    public function testGetId_WithValidData_ReturnsUuidV7(): void
    {
        // ARRANGE & ACT
        $result = $this->subject->getId();

        // ASSERT
        $this->assertInstanceOf(UuidV7::class, $result);
    }

    public function testJsonSerialize_WithValidData_ReturnsArray(): void
    {
        // ARRANGE & ACT
        $result = $this->subject->jsonSerialize();

        // ASSERT
        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', $result);
        $this->assertSame($this->subject->getId(), $result['id']);
        $this->assertArrayHasKey('name', $result);
        $this->assertSame('Test product', $result['name']);
    }
}