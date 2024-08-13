<?php

declare(strict_types=1);

namespace App\Core\Application\Query\Questionnaire\GetQuestionnaire;

use Symfony\Component\Uid\UuidV7;

final readonly class GetQuestionnaireQuery
{
    public function __construct(
        public UuidV7 $id,
    ) {
    }
}