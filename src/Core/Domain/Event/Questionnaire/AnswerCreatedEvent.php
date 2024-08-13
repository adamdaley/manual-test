<?php

declare(strict_types=1);

namespace App\Core\Domain\Event\Questionnaire;

use App\Shared\Domain\Event\DomainEventInterface;
use Symfony\Component\Uid\UuidV7;

final readonly class AnswerCreatedEvent implements DomainEventInterface
{
    /**
     * @param list<UuidV7> $productIdRestrictions
     */
    public function __construct(
        public UuidV7 $id,
        public UuidV7 $questionnaireId,
        public UuidV7 $questionId,
        public string $title,
        public ?UuidV7 $nextQuestionId,
        public array $productIdRestrictions,
    ) {
    }
}