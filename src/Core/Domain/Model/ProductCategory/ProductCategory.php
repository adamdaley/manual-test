<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\ProductCategory;

use App\Core\Domain\Model\ProductCategory\Exception\ProductNotFoundException;
use App\Shared\Domain\Model\Aggregate;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping;
use InvalidArgumentException;
use JsonSerializable;
use Symfony\Component\Uid\UuidV7;

#[Mapping\Entity]
class ProductCategory extends Aggregate implements JsonSerializable
{
    /** @var Collection<int, Product> */
    #[Mapping\OneToMany(targetEntity: Product::class, mappedBy: 'productCategory', cascade: ['persist', 'remove'])]
    private Collection $products;

    public function __construct(
        #[Mapping\Column(type: Types::STRING)]
        public readonly string $name,
    ) {
        if (empty($this->name)) {
            throw new InvalidArgumentException('ProductCategory name cannot be empty.');
        }

        parent::__construct();

        $this->products = new ArrayCollection();

        // todo record event
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    /**
     * @throws ProductNotFoundException
     */
    public function getProductById(UuidV7 $productId): Product
    {
        return $this->products->findFirst(fn(int $key, Product $product) => $product->getId()->equals($productId))
            ?? throw new ProductNotFoundException($productId);
    }

    public function addProduct(string $name): Product
    {
        $product = $this->products[] = new Product($this, $name);

        // todo record event

        return $product;
    }

    /**
     * @throws ProductNotFoundException
     */
    public function removeProduct(UuidV7 $productId): void
    {
        $product = $this->getProductById($productId);

        $this->products->removeElement($product);

        // todo record event
    }

    /**
     * @param UuidV7[] $restrictionIds
     *
     * @return Collection<int, Product>
     */
    public function filterProductsByRestrictionIds(array $restrictionIds): Collection
    {
        return $this->products->filter(fn(Product $product) => !in_array($product->getId(), $restrictionIds));
    }

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'products' => $this->products->map(fn(Product $product) => $product->jsonSerialize())->toArray(),
        ];
    }
}