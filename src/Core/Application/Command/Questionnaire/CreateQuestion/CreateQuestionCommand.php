<?php

declare(strict_types=1);

namespace App\Core\Application\Command\Questionnaire\CreateQuestion;

use Symfony\Component\Uid\UuidV7;

final readonly class CreateQuestionCommand
{
    public function __construct(
        public UuidV7 $questionnaireId,
        public string $title,
    ) {
    }
}