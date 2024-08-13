<?php

declare(strict_types=1);

namespace App\Shared\Domain\Model;

use App\Shared\Domain\Event\DomainEventInterface;
use Doctrine\ORM\Mapping;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Uid\UuidV7;

abstract class Aggregate implements EntityInterface
{
    #[Mapping\Id]
    #[Mapping\Column(type: UuidType::NAME, unique: true)]
    public readonly UuidV7 $id;

    /**
     * @var DomainEventInterface[]
     */
    private array $events = [];

    public function __construct()
    {
        $this->id = Uuid::v7();
    }

    public function getId(): UuidV7
    {
        return $this->id;
    }

    /**
     * @return DomainEventInterface[]
     */
    public function popEvents(): array
    {
        $events = $this->events;
        $this->events = [];

        return $events;
    }

    protected function raise(DomainEventInterface $event): void
    {
        $this->events[] = $event;
    }
}