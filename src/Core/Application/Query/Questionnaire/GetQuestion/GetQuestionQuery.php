<?php

declare(strict_types=1);

namespace App\Core\Application\Query\Questionnaire\GetQuestion;

use Symfony\Component\Uid\UuidV7;

final readonly class GetQuestionQuery
{
    public function __construct(
        public UuidV7 $questionnaireId,
        public UuidV7 $questionId,
    ) {
    }
}