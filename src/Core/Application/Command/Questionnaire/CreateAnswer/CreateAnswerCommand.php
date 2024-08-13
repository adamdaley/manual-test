<?php

declare(strict_types=1);

namespace App\Core\Application\Command\Questionnaire\CreateAnswer;

use Symfony\Component\Uid\UuidV7;

final readonly class CreateAnswerCommand
{
    /**
     * @param list<UuidV7> $productIdRestrictions
     */
    public function __construct(
        public UuidV7 $questionnaireId,
        public UuidV7 $questionId,
        public string $title,
        public ?UuidV7 $nextQuestionId = null,
        public array $productIdRestrictions = [],
    ) {
    }
}