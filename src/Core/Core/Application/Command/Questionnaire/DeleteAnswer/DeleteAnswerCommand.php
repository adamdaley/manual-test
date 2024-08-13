<?php

declare(strict_types=1);

namespace App\Core\Application\Command\Questionnaire\DeleteAnswer;

use Symfony\Component\Uid\UuidV7;

final readonly class DeleteAnswerCommand
{
    public function __construct(
        public UuidV7 $questionnaireId,
        public UuidV7 $questionId,
        public UuidV7 $answerId,
    ) {
    }
}