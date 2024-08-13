<?php

declare(strict_types=1);

namespace App\Core\Application\Command\Questionnaire\DeleteQuestion;

use Symfony\Component\Uid\UuidV7;

final readonly class DeleteQuestionCommand
{
    public function __construct(
        public UuidV7 $questionnaireId,
        public UuidV7 $questionId,
    ) {
    }
}