<?php

declare(strict_types=1);

namespace App\Shared\Infrastructure\Repository;

use Symfony\Component\Uid\UuidV7;

/**
 * @template T of object
 */
interface AggregateRepositoryInterface
{
    /**
     * @return T
     */
    public function find(UuidV7 $id): object;

    /**
     * @return list<T>
     */
    public function findAll(): array;

    /**
     * @param T $object
     */
    public function add(object $object): void;

    /**
     * @param T $object
     */
    public function remove(object $object): void;
}