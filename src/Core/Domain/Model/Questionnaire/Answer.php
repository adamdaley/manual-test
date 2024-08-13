<?php

declare(strict_types=1);

namespace App\Core\Domain\Model\Questionnaire;

use App\Shared\Domain\Model\EntityInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping;
use InvalidArgumentException;
use JsonSerializable;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV7;

#[Mapping\Entity]
class Answer implements EntityInterface, JsonSerializable
{
    #[Mapping\Id]
    #[Mapping\Column(type: UuidType::NAME, unique: true)]
    private UuidV7 $id;

    /**
     * @var list<string> $productIdRestrictions
     */
    #[Mapping\Column(type: Types::JSON)]
    private array $productIdRestrictions = [];

    /**
     * @param list<UuidV7> $productIdRestrictions
     */
    public function __construct(
        #[Mapping\ManyToOne(targetEntity: Question::class)]
        #[Mapping\JoinColumn(nullable: false)]
        public readonly Question $question,
        #[Mapping\Column(type: Types::STRING)]
        public readonly string $title,
        #[Mapping\Column(type: UuidType::NAME, nullable: true)]
        private ?UuidV7 $nextQuestionId = null,
        array $productIdRestrictions = [],
    ) {
        if (empty($this->title)) {
            throw new InvalidArgumentException('Answer title cannot be empty.');
        }

        if (
            !empty($productIdRestrictions)
            && array_reduce(
                $productIdRestrictions,
                fn(bool $carry, Uuid $productId) => $carry && $productId instanceof UuidV7, true,
            ) === false
        ) {
            throw new InvalidArgumentException('Answer product id restrictions must be an array of Uuids.');
        }

        $this->id = Uuid::v7();
        $this->productIdRestrictions = array_map(fn(UuidV7 $productIdRestriction): string => (string) $productIdRestriction, $productIdRestrictions);
    }

    public function getId(): UuidV7
    {
        return $this->id;
    }

    public function getNextQuestionId(): ?UuidV7
    {
        return $this->nextQuestionId;
    }

//    public function setNextQuestionId(?UuidV7 $nextQuestionId): void
//    {
//        $this->nextQuestionId = $nextQuestionId;
//    }

    /**
     * @return list<UuidV7>
     */
    public function getProductIdRestrictions(): array
    {
        return array_map(fn(string $productIdRestriction): UuidV7 => UuidV7::fromString($productIdRestriction), $this->productIdRestrictions);
    }

//    public function addProductIdRestriction(UuidV7 $productId): void
//    {
//        $this->productIdRestrictions[] = $productId;
//    }
//
//    public function removeProductIdRestriction(UuidV7 $productId): void
//    {
//        $key = array_search($productId, $this->productIdRestrictions);
//        if ($key !== false) {
//            unset($this->productIdRestrictions[$key]);
//        }
//    }

    public function jsonSerialize(): mixed
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'nextQuestionId' => $this->nextQuestionId,
            'productIdRestrictions' => $this->getProductIdRestrictions(),
        ];
    }
}