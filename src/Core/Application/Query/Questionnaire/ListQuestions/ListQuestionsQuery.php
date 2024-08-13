<?php

declare(strict_types=1);

namespace App\Core\Application\Query\Questionnaire\ListQuestions;

use Symfony\Component\Uid\UuidV7;

final readonly class ListQuestionsQuery
{
    public function __construct(
        public UuidV7 $questionnaireId
    ) {
    }
}