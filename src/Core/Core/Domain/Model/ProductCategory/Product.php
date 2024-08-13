<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\ProductCategory;

use App\Shared\Domain\Model\EntityInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping;
use InvalidArgumentException;
use JsonSerializable;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV7;

#[Mapping\Entity]
class Product implements EntityInterface, JsonSerializable
{
    #[Mapping\Id]
    #[Mapping\Column(type: UuidType::NAME, unique: true)]
    private UuidV7 $id;

    public function __construct(
        #[Mapping\ManyToOne(targetEntity: ProductCategory::class)]
        #[Mapping\JoinColumn(nullable: false)]
        public readonly ProductCategory $productCategory,
        #[Mapping\Column(type: Types::STRING)]
        public readonly string $name,
    ) {
        if (empty($this->name)) {
            throw new InvalidArgumentException('Product name cannot be empty.');
        }

        $this->id = Uuid::v7();
    }

    public function getId(): UuidV7
    {
        return $this->id;
    }

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
        ];
    }
}