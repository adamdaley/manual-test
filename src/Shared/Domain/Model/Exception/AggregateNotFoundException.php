<?php

declare(strict_types=1);

namespace App\Shared\Domain\Model\Exception;

use Exception;
use Symfony\Component\Uid\UuidV7;

class AggregateNotFoundException extends Exception
{
    public function __construct(string $aggregateName, UuidV7 $aggregateId)
    {
        parent::__construct(message: sprintf('%s with id "%s" could not be found.', $aggregateName, $aggregateId));
    }
}