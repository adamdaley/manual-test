<?php

declare(strict_types=1);

namespace App\Core\Domain\Event\Questionnaire;

use App\Shared\Domain\Event\DomainEventInterface;
use Symfony\Component\Uid\UuidV7;

final readonly class QuestionnaireCreatedEvent implements DomainEventInterface
{
    public function __construct(
        public UuidV7 $id,
        public UuidV7 $productCategoryId,
        public string $title,
    ) {
    }
}