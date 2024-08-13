<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Repository;

use App\Shared\Domain\Model\Exception\AggregateNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Uid\UuidV7;

/**
 * @template T of object
 *
 * @implements AggregateRepositoryInterface<T>
 */
abstract class AbstractAggregateRepository implements AggregateRepositoryInterface
{
    /** @var EntityRepository<T> */
    protected readonly EntityRepository $repository;

    /**
     * @param class-string<T> $aggregateClass
     */
    public function __construct(
        protected readonly EntityManagerInterface $entityManager,
        string $aggregateClass,
    ) {
        $this->repository = $this->entityManager->getRepository($aggregateClass);
    }

    /**
     * @return T
     *
     * @throws AggregateNotFoundException
     */
    public function find(UuidV7 $id): object
    {
        $model = $this->repository->find($id);

        if (!$model instanceof ($this->repository->getClassName())) {
            throw new AggregateNotFoundException($this->repository->getClassName(), $id);
        }

        return $model;
    }

    /**
     * @return list<T>
     */
    public function findAll(): array
    {
        return $this->repository->findAll();
    }

    /**
     * @param T $object
     */
    public function add(object $object): void
    {
        $this->entityManager->persist($object);
    }

    /**
     * @param T $object
     */
    public function remove(object $object): void
    {
        $this->entityManager->remove($object);
    }
}