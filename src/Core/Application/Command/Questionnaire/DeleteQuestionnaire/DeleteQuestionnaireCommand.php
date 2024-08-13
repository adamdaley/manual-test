<?php

declare(strict_types=1);

namespace App\Core\Application\Command\Questionnaire\DeleteQuestionnaire;

use Symfony\Component\Uid\UuidV7;

final readonly class DeleteQuestionnaireCommand
{
    public function __construct(
        public UuidV7 $id,
    ) {
    }
}