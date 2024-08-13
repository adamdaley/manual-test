<?php

declare(strict_types=1);

namespace App\Core\Application\Command\Questionnaire\CreateQuestionnaire;

use Symfony\Component\Uid\UuidV7;

final readonly class CreateQuestionnaireCommand
{
    public function __construct(
        public UuidV7 $productCategoryId,
        public string $title,
    ) {
    }
}