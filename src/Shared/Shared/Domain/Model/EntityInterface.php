<?php

declare(strict_types=1);

namespace App\Shared\Domain\Model;

use Symfony\Component\Uid\UuidV7;

interface EntityInterface
{
    public function getId(): UuidV7;
}