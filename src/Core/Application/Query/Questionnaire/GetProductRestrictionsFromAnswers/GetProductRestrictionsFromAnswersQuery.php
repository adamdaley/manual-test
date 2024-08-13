<?php

declare(strict_types=1);

namespace App\Core\Application\Query\Questionnaire\GetProductRestrictionsFromAnswers;

use Symfony\Component\Uid\UuidV7;

final readonly class GetProductRestrictionsFromAnswersQuery
{
    /**
     * @param list<UuidV7> $answerIds
     */
    public function __construct(
        public UuidV7 $questionnaireId,
        public array $answerIds,
    ) {
    }
}